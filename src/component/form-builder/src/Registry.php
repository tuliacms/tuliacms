<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var array|iterable
     */
    protected $extensions = [];

    /**
     * @var array|iterable
     */
    protected $extensionsAggregate = [];

    /**
     * @var bool
     */
    protected $aggregatesUnpacked = false;

    /**
     * @param array|iterable $extensions
     * @param array|iterable $extensionsAggregate
     */
    public function __construct(iterable $extensions = [], iterable $extensionsAggregate = [])
    {
        $this->extensions = $extensions;
        $this->extensionsAggregate = $extensionsAggregate;
    }

    public function all(): iterable
    {
        $this->unpackAggregates();

        return $this->extensions;
    }

    public function add(ExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
    }

    public function getSupportive(object $object, string $scope): iterable
    {
        $this->unpackAggregates();

        $supportive = [];

        /** @var ExtensionInterface $extension */
        foreach ($this->extensions as $extension) {
            if ($extension->supports($object, $scope)) {
                $supportive[] = $extension;
            }
        }

        return $supportive;
    }

    private function unpackAggregates(): void
    {
        if ($this->aggregatesUnpacked) {
            return;
        }

        /** @var ExtensionAggregateInterface $aggregate */
        foreach ($this->extensionsAggregate as $key => $aggregate) {
            /** @var ExtensionInterface $extension */
            foreach ($aggregate->aggregate() as $extension) {
                $this->add($extension);
            }
        }

        $this->aggregatesUnpacked = true;
    }
}
