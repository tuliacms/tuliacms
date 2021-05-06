<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Application\Service;

/**
 * @author Adam Banaszkiewicz
 */
class RegisteredOptionsCollector
{
    private array $providers;

    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    public function collectRegisteredOptions(): array
    {
        $result = [];

        foreach ($this->providers as $file) {
            $result[] = include $file;
        }

        $result = array_merge(...$result);

        foreach ($result as $key => $val) {
            if (isset($val['value']) === false) {
                $result[$key]['value'] = null;
            }
            if (isset($val['multilingual']) === false) {
                $result[$key]['multilingual'] = false;
            }
            if (isset($val['autoload']) === false) {
                $result[$key]['autoload'] = false;
            }
        }

        return $result;
    }
}
