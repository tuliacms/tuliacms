<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Domain\WriteModel;

use Tulia\Cms\Options\Domain\WriteModel\Exception\OptionNotFoundException;
use Tulia\Cms\Options\Domain\WriteModel\Model\Option;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class OptionRepository implements OptionsRepositoryInterface
{
    private OptionsStorageInterface $storage;
    private CurrentWebsiteInterface $currentWebsite;
    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        OptionsStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->uuidGenerator = $uuidGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $name): Option
    {
        $option = $this->storage->find(
            $name,
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode()
        );

        if ($option === null) {
            throw new OptionNotFoundException(sprintf('Option named "%s" not found.', $name));
        }

        return $this->createOption($option);
    }

    /**
     * {@inheritdoc}
     */
    public function findAllForWebsite(string $websiteId): array
    {
        $options = [];
        $source = $this->storage->findAllForWebsite(
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode()
        );

        foreach ($source as $row) {
            $options[] = $this->createOption($row);
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Option $option): void
    {
        $this->saveBulk([$option]);
    }

    /**
     * {@inheritdoc}
     */
    public function saveBulk(array $options): void
    {
        foreach ($options as $option) {
            $this->storage->insert(
                $this->extractOption($option),
                $this->currentWebsite->getDefaultLocale()->getCode()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(Option $option): void
    {
        $this->updateBulk([$option]);
    }

    /**
     * {@inheritdoc}
     */
    public function updateBulk(array $options): void
    {
        foreach ($options as $option) {
            $this->storage->update(
                $this->extractOption($option),
                $this->currentWebsite->getDefaultLocale()->getCode()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Option $option): void
    {
        $this->deleteBulk([$option]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteBulk(array $options): void
    {
        foreach ($options as $option) {
            $this->storage->delete([
                'name' => $option->getName(),
                'website_id' => $option->getWebsiteId(),
            ]);
        }
    }

    private function createOption(array $row): Option
    {
        $option = new Option(
            $row['website_id'],
            $row['name'],
            $row['value'],
            $row['locale'],
            (bool) $row['multilingual'],
            (bool) $row['autoload'],
        );
        $option->setId($row['id']);

        return $option;
    }

    private function extractOption(Option $option): array
    {
        return [
            'id' => $option->getId() ?? $this->uuidGenerator->generate(),
            'website_id' => $option->getWebsiteId() ?? $this->currentWebsite->getId(),
            'name' => $option->getName(),
            'value' => $option->getValue(),
            'locale' => $option->getLocale() ?? $this->currentWebsite->getLocale()->getCode(),
            'multilingual' => $option->isMultilingual(),
            'autoload' => $option->isAutoload(),
        ];
    }
}
