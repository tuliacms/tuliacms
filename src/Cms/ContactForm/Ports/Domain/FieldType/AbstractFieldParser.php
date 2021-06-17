<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Ports\Domain\FieldType;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractFieldParser implements FieldParserInterface
{
    protected function parseConstraints(?string $constraints): array
    {
        if (empty($constraints)) {
            return [];
        }

        $result = [];

        foreach (explode('|', $constraints) as $def) {
            $param = '';

            if (strpos($def, ':') > 0) {
                [$def, $param] = explode(':', $def);
            }

            $result[$def] = $param;
        }

        return $result;
    }
}
