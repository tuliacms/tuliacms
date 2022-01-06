<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\LayoutTypeProvider;

use Tulia\Cms\ContentBuilder\Domain\LayoutType\Service\AbstractLayoutTypeProvider;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseProvider extends AbstractLayoutTypeProvider
{
    private ConnectionInterface $connection;
    private array $groupsSource = [];
    private array $fieldsSource = [];

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function provide(): array
    {
        $result = [];

        foreach ($this->connection->fetchAllAssociative('SELECT * FROM #__node_type_layout') as $type) {
            $type['sections']['main']['groups'] = $this->getGroups($type['code'], 'main');
            $type['sections']['sidebar']['groups'] = $this->getGroups($type['code'], 'sidebar');

            $result[] = $this->buildLayoutType($type['code'], $type);
        }

        return $result;
    }

    private function getGroups(string $layoutCode, string $section): array
    {
        if ($this->groupsSource === []) {
            $this->groupsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__node_type_layout_group ORDER BY `order` ASC');
        }

        $result = [];

        foreach ($this->groupsSource as $group) {
            if ($group['section'] === $section && $group['layout_type'] === $layoutCode) {
                $result[$group['code']] = $group;
                $result[$group['code']]['fields'] = $this->getFields($group['id']);
                $result[$group['code']]['interior'] = 'default';
            }
        }

        return $result;
    }

    private function getFields(string $groupId): array
    {
        if ($this->fieldsSource === []) {
            $this->fieldsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__node_type_layout_group_field ORDER BY `order` ASC');
        }

        $result = [];

        foreach ($this->fieldsSource as $field) {
            if ($field['group_id'] === $groupId) {
                $result[] = $field['code'];
            }
        }

        return $result;
    }
}
