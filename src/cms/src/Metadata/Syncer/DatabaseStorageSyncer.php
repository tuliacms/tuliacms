<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Syncer;

use Tulia\Cms\Metadata\Registrator\RegistryInterface;
use Tulia\Cms\Metadata\Storage\DatabaseStorage;
use Tulia\Cms\Metadata\MetadataInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorageSyncer implements SyncerInterface
{
    /**
     * @var DatabaseStorage
     */
    protected $storage;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param DatabaseStorage $storage
     * @param RegistryInterface $registry
     */
    public function __construct(DatabaseStorage $storage, RegistryInterface $registry)
    {
        $this->storage  = $storage;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function all(string $type, string $id): array
    {
        $fields = $this->registry->getContentFields($type);
        $source = $this->storage->getMany($type, $id, $fields->getNames());
        $result = [];

        foreach ($fields as $name => $field) {
            if (isset($source[$name])) {
                switch ($field['datatype']) {
                    case 'array':
                        @ $tmp = unserialize($source[$name], ['allowed_classes' => []]);

                        if(\is_array($tmp)) {
                            $value = $tmp;
                        } else {
                            $value = $field['default'];
                        }

                        break;
                    default:
                        $value = $source[$name];
                }

                $result[$name] = $value;

                continue;
            }

            $result[$name] = $field['default'];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function pull(MetadataInterface $metadata, string $type, string $id): void
    {
        foreach ($this->all($type, $id) as $name => $value) {
            $metadata->set($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function push(MetadataInterface $metadata, string $type, string $id): void
    {
        $fields = $this->registry->getContentFields($type);

        foreach ($metadata->keys() as $name) {
            if ($fields->has($name) === false) {
                continue;
            }

            $field = $fields->get($name);

            switch ($field['datatype']) {
                case 'array':
                    $value = serialize($metadata->get($name));
                    break;
                default:
                    $value = $metadata->get($name);
            }

            $this->storage->set($type, $id, $name, $value, $fields->get($name)['multilingual']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $type, string $id, array $entries = []): void
    {
        $fields = $this->registry->getContentFields($type);

        if ($entries === []) {
            $this->storage->deleteAll($type, $id);
        } else {
            foreach ($entries as $name) {
                if ($fields->has($name) === false) {
                    continue;
                }

                $this->storage->delete($type, $id, $name);
            }
        }
    }
}
