<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Changeset\Factory;

use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Theme\Customizer\Changeset\Changeset;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ChangesetFactory implements ChangesetFactoryInterface
{
    protected UuidGeneratorInterface $uuidGenerator;
    protected string $classname;

    public function __construct(UuidGeneratorInterface $uuidGenerator, string $classname = Changeset::class)
    {
        $this->uuidGenerator = $uuidGenerator;
        $this->classname = $classname;
    }

    /**
     * {@inheritdoc}
     */
    public function factory(string $id = null): ChangesetInterface
    {
        $classname = $this->classname;

        return new $classname($id ?? $this->uuidGenerator->generate());
    }
}
