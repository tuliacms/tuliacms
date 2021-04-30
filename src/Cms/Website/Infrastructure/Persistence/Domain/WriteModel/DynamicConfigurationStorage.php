<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Domain\WriteModel;

/**
 * @author Adam Banaszkiewicz
 */
class DynamicConfigurationStorage
{
    private string $filepath;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    public function save(array $data): void
    {
        if (is_writable($this->filepath) === false) {
            throw new \RuntimeException('Websites dynamic configuration file is not writabe. Cannot save new configuration.');
        }

        file_put_contents($this->filepath, sprintf('<?php return %s;', var_export($data, true)));
    }
}
