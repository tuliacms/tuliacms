<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata;

use Tulia\Cms\Metadata\Syncer\SyncerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LazyMetadata extends Metadata
{
    private bool $loaded = false;
    private SyncerInterface $syncer;
    private string $type;
    private string $id;

    public static function create(SyncerInterface $syncer, string $type, string $id): self
    {
        $self = new self();
        $self->syncer = $syncer;
        $self->type = $type;
        $self->id = $id;

        return $self;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        $this->load();

        return parent::get($name, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $name): bool
    {
        $this->load();

        return parent::has($name);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        $this->load();

        return parent::all();
    }

    private function load(): void
    {
        if ($this->loaded) {
            return;
        }

        $this->syncer->pull($this, $this->type, $this->id);

        $this->loaded = true;
    }
}
