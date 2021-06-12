<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode;

/**
 * @author Adam Banaszkiewicz
 */
interface ProcessorInterface
{
    public const INLINE_HTML_TAGS = [
        'a', 'abbr', 'acronym', 'audio', 'b', 'bdi', 'bdo', 'big', 'br', 'button', 'canvas', 'cite',
        'code', 'data', 'datalist', 'del', 'dfn', 'em', 'embed', 'i', 'iframe', 'img', 'input', 'ins',
        'kbd', 'label', 'map', 'mark', 'meter', 'noscript', 'object', 'output', 'picture', 'progress', 'q',
        'ruby', 's', 'samp', 'script', 'select', 'slot', 'small', 'span', 'strong', 'sub', 'sup', 'svg',
        'template', 'textarea', 'time', 'u', 'tt', 'var', 'video', 'wbr',
    ];

    public const HTML_TAG_PATTERN = '/^<(%s)( |>)/i';

    public function process(string $input): string;
}
