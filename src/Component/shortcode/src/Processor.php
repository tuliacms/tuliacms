<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode;

use Symfony\Component\Process\Process;
use Thunder\Shortcode\HandlerContainer\HandlerContainer;
use Thunder\Shortcode\Parser\RegularParser;
use Thunder\Shortcode\Processor\Processor as BaseProcessor;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;
use Thunder\Shortcode\Event\ReplaceShortcodesEvent;
use Thunder\Shortcode\EventContainer\EventContainer;
use Thunder\Shortcode\Events;
use Tulia\Component\Shortcode\Registry\CompilerRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Processor implements ProcessorInterface
{
    protected CompilerRegistryInterface $compilers;

    protected ?BaseProcessor $processor = null;

    public function __construct(CompilerRegistryInterface $compilers)
    {
        $this->compilers = $compilers;
    }

    public function process(string $input): string
    {
        return $this->getProcessor()->process($this->prepareInput($input));
    }

    public function prepareInput(string $input): string
    {
        return preg_replace('~\R~u', "\n", $input);
    }

    private function getProcessor(): BaseProcessor
    {
        if ($this->processor) {
            return $this->processor;
        }

        $handlers = new HandlerContainer();

        foreach ($this->compilers->all() as $compiler) {
            $handlers->add($compiler->getAlias(), function(ShortcodeInterface $s) use ($compiler) {
                $shortcode = new Shortcode($s->getName(), $s->getParameters(), $s->getContent());
                return $compiler->compile($shortcode);
            });
        }

        $events = new EventContainer();
        $events->addListener(Events::REPLACE_SHORTCODES, function(ReplaceShortcodesEvent $event) {
            if ($event->getShortcode() || ! $event->getReplacements()) {
                return;
            }

            $source = $event->getText();
            $result = $event->getText();

            $htmlTagPattern = sprintf(
                ProcessorInterface::HTML_TAG_PATTERN,
                implode('|', ProcessorInterface::INLINE_HTML_TAGS)
            );

            foreach (array_reverse($event->getReplacements()) as $replacement) {
                $inputAfterShortcode = mb_substr($source, $replacement->getOffset() + mb_strlen($replacement->getText()));
                $shortcodeInfo = [
                    'tagBefore' => '',
                    'tagAfter'  => '',
                ];

                /**
                 * If we have a ending HTML tag after the shortcode, we have also a
                 * opening HTML tag before shortcode. Any block tag will not destroy
                 * compilation, but inline tags, like <p> or <a> can, so we need to
                 * found them, and remove in compilation time.
                 */
                if (preg_match(\Tulia\Component\Shortcode\ShortcodeInterface::CLOSE_TAG_PATTERN, $inputAfterShortcode, $tagAfter)) {
                    $inputBeforeShortcode = mb_substr($source, 0, $replacement->getOffset());

                    if (preg_match(\Tulia\Component\Shortcode\ShortcodeInterface::OPEN_TAG_PATTERN, $inputBeforeShortcode, $tagBefore)) {
                        $shortcodeInfo['tagBefore'] = ltrim($tagBefore[0]);
                        $shortcodeInfo['tagAfter']  = rtrim($tagAfter[0]);
                    }
                }

                preg_match($htmlTagPattern, $shortcodeInfo['tagBefore'], $matches);

                if (isset($matches[1]) === false || \in_array($matches[1], ProcessorInterface::INLINE_HTML_TAGS, true) === false) {
                    $before = 0;
                    $after  = 0;
                } else {
                    $before = mb_strlen($shortcodeInfo['tagBefore']);
                    $after  = mb_strlen($shortcodeInfo['tagAfter']);
                }

                $offset = $replacement->getOffset() - $before;
                $length = mb_strlen($replacement->getText(), 'utf-8') + $before + $after;
                $textLength = mb_strlen($result, 'utf-8');

                $result = mb_substr($result, 0, $offset, 'utf-8').$replacement->getReplacement().mb_substr($result, $offset + $length, $textLength, 'utf-8');
            }

            $event->setResult($result);
        });

        $this->processor = new BaseProcessor(new RegularParser(), $handlers);
        $this->processor = $this->processor->withEventContainer($events);

        return $this->processor;
    }
}
