<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;
use Tulia\Cms\Filemanager\Domain\Generator\Html;

/**
 * @author Adam Banaszkiewicz
 */
class Image implements ShortcodeCompilerInterface
{
    /**
     * {@inheritdoc}
     */
    public function compile(ShortcodeInterface $shortcode): string
    {
        if ($src = $shortcode->getParameter('src')) {
            return $this->compileSrc($shortcode, $src);
        }

        if ($id = $shortcode->getParameter('id')) {
            return $this->compileId($shortcode, $id);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'image';
    }

    private function compileSrc(ShortcodeInterface $shortcode, string $src): string
    {
        $attributes = $this->collectAttributes($shortcode);
        $attributes['src'] = "{{ asset('{$src}') }}";

        return (new Html())->generateImageTag($attributes);
    }

    private function compileId(ShortcodeInterface $shortcode, string $id): string
    {
        $size    = $shortcode->getParameter('size');
        $version = $shortcode->getParameter('version');
        $attributes = $this->collectAttributes($shortcode);

        $hash = [];

        foreach ($attributes as $name => $val) {
            $hash[] = "$name: '$val'";
        }

        $hash = '{' . implode(', ', $hash) . '}';

        return "{{ image('{$id}', {attributes: {$hash}, size: '{$size}', version: '{$version}'}) }}";
    }

    private function collectAttributes(ShortcodeInterface $shortcode): array
    {
        $attributes = array_filter([
            'title'   => $shortcode->getParameter('title'),
            'id'      => $shortcode->getParameter('html-id'),
            'class'   => $shortcode->getParameter('html-class'),
            'caption' => $shortcode->getParameter('caption'),
        ]);
        $attributes['alt'] = $shortcode->getParameter('alt');

        return $attributes;
    }
}
