<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
trait LayoutTypeDatabaseProviderTrait
{
    private ConnectionInterface $connection;
    private array $layoutsSource = [];
    private array $groupsSource = [];
    private array $fieldsListSource = [];
    private array $layoutsTypes = [];

    public function getLayoutType(string $layoutCode): array
    {
        $this->prefetch();

        return $this->layoutsTypes[$layoutCode];
    }

    private function prefetch(): void
    {
        if ($this->layoutsSource === []) {
            $this->layoutsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_layout');
        }

        if ($this->layoutsTypes === []) {
            foreach ($this->layoutsSource as $type) {
                $type['sections']['main']['groups'] = $this->getGroups($type['code'], 'main');
                $type['sections']['sidebar']['groups'] = $this->getGroups($type['code'], 'sidebar');

                $this->layoutsTypes[$type['code']] = $type;
            }
        }
    }

    private function getGroups(string $layoutCode, string $section): array
    {
        if ($this->groupsSource === []) {
            $this->groupsSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_layout_group ORDER BY `order` ASC');
        }

        $result = [];

        foreach ($this->groupsSource as $group) {
            if ($group['section'] === $section && $group['layout_type'] === $layoutCode) {
                $result[$group['code']] = $group;
                $result[$group['code']]['fields'] = $this->getLayoutFields($group['id']);
                $result[$group['code']]['interior'] = 'default';
            }
        }

        return $result;
    }

    private function getLayoutFields(string $groupId): array
    {
        if ($this->fieldsListSource === []) {
            $this->fieldsListSource = $this->connection->fetchAllAssociative('SELECT * FROM #__content_type_layout_group_field ORDER BY `order` ASC');
        }

        $result = [];

        foreach ($this->fieldsListSource as $field) {
            if ($field['group_id'] === $groupId) {
                $result[] = $field['code'];
            }
        }

        return $result;
    }
}
