<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Ports\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Website\Domain\WriteModel\Exception\WebsiteNotFoundException;
use Tulia\Cms\Website\Domain\WriteModel\Model\Website;

/**
 * @author Adam Banaszkiewicz
 */
interface WebsiteRepositoryInterface
{
    public function createNew(array $data = []): Website;

    /**
     * @param string $id
     * @return Website
     * @throws WebsiteNotFoundException
     */
    public function find(string $id): Website;

    public function create(Website $website): void;

    public function update(Website $website): void;

    public function delete(string $id): void;
}
