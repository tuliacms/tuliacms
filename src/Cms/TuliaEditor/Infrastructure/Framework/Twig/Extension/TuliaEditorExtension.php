<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Metadata\Ports\Domain\WriteModel\MetadataAwareInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaEditorExtension extends AbstractExtension
{
    protected EngineInterface $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('tulia_editor', function (string $name, ?string $content, MetadataAwareInterface $entity, array $params = []) {
                if (isset($params['id']) === false) {
                    $params['id'] = uniqid('', true);
                }

                return $this->engine->render(new View('@backend/tulia-editor/editor.tpl', [
                    'name' => $name,
                    'entity' => $entity,
                    'content' => $content,
                    'params' => $params,
                ]));
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
