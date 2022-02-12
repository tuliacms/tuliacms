<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Domain\WriteModel\Service;

use Tulia\Cms\Metadata\Domain\WriteModel\Model\Attribute;

/**
 * @author Adam Banaszkiewicz
 */
class AttributesToArrayTransformer
{
    /**
     * @param Attribute[] $attributes
     */
    public function transform(array $attributes): array
    {
        $output = [];

        foreach ($attributes as $attribute) {
            parse_str($attribute->getUri().'=v', $result);

            $value = $this->assignValueToMostDeepIndex($result, $attribute->getValue());
            $output = $this->mergeRecursive($output, $value);
        }

        return $output;
    }

    private function assignValueToMostDeepIndex(&$input, $value)
    {
        if (is_array($input)) {
            foreach ($input as &$item) {
                $this->assignValueToMostDeepIndex($item, $value);
            }

            unset($item);
        }

        if ($input === 'v') {
            $input = $value;
        }

        return $input;
    }

    private function mergeRecursive(&$target, $value)
    {
        foreach ($value as $key => &$item) {
            if (is_array($item)) {
                if (isset($target[$key]) === false) {
                    $target[$key] = [];
                }

                $target[$key] = $this->mergeRecursive($target[$key], $item);
            } else {
                $target[$key] = $item;
            }
        }

        return $this->sortArrayByKey($target);
    }

    private function sortArrayByKey(array $array): array
    {
        ksort($array);

        return $array;
    }
}
