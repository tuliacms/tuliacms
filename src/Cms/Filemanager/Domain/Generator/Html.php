<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\Generator;

/**
 * @author Adam Banaszkiewicz
 */
class Html
{
    public function generateImageTag(array $image): string
    {
        $caption = '';

        if (isset($image['caption'])) {
            $caption = "<figcaption>{$image['caption']}</figcaption>";
        }

        unset($image['caption']);

        $attributes = $this->renderHtmlAttributes($image);

        return "<figure><img {$attributes} />{$caption}</figure>";
    }

    private function renderHtmlAttributes(array $attributes): string
    {
        $result = [];

        foreach ($attributes as $name => $val) {
            $result[] = $name . '="' . $val . '"';
        }

        return implode(' ', $result);
    }
}
