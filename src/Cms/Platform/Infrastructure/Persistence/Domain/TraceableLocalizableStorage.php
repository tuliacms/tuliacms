<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Persistence\Domain;

/**
 * @author Adam Banaszkiewicz
 */
class TraceableLocalizableStorage extends AbstractLocalizableStorage
{
    /**
     * @var TraceInterface
     */
    private $trace;

    public function __construct(TraceInterface $trace)
    {
        $this->trace = $trace;
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $this->trace->updateMainRow($data, $foreignLocale);
    }

    protected function insertMainRow(array $data): void
    {
        $this->trace->insertMainRow($data);
    }

    protected function insertLangRow(array $data): void
    {
        $this->trace->insertLangRow($data);
    }

    protected function updateLangRow(array $data): void
    {
        $this->trace->updateLangRow($data);
    }

    protected function rootExists(string $id): bool
    {
        return $this->trace->rootExists($id);
    }

    protected function langExists(array $data): bool
    {
        return $this->trace->langExists($data);
    }
}
