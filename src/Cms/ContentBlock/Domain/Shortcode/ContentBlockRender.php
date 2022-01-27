<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBlock\Domain\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBlockRender implements ShortcodeCompilerInterface
{
    /**
     * {@inheritdoc}
     */
    public function compile(ShortcodeInterface $shortcode): string
    {
        return "{{ content_block_render('{$shortcode->getParameter('source')}') }}";
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'content_block_render';
    }
}
