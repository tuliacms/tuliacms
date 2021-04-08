<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\Application\Service\Steps;

use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class InstallationFinisher
{
    /**
     * @var UuidGeneratorInterface
     */
    private $uuidGenerator;

    /**
     * @var string
     */
    private $projectDir;

    public function __construct(UuidGeneratorInterface $uuidGenerator, string $projectDir)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->projectDir = $projectDir;
    }

    public function finish(): void
    {
        $appkey = $this->uuidGenerator->generate();

        file_put_contents($this->projectDir . '/.env', "APP_KEY={$appkey}\n", FILE_APPEND);
    }
}
