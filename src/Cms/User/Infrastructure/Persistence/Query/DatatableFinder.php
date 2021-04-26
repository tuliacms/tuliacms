<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Query;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFinder extends AbstractDatatableFinder
{
    private RouterInterface $router;
    private TranslatorInterface $translator;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        parent::__construct($connection, $currentWebsite);

        $this->router = $router;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationKey(): string
    {
        return __CLASS__;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns(): array
    {
        return [
            'id' => [
                'selector' => 'tm.id',
                'type' => 'uuid',
                'label' => 'ID',
            ],
            'username' => [
                'selector' => 'tm.username',
                'label' => 'username',
                'sortable' => true,
                'html_attr' => [
                    'class' => 'col-title',
                ],
            ],
            'enabled' => [
                'selector' => 'tm.enabled',
                'label' => 'enabled',
                'sortable' => true,
                'value_translation' => [
                    '1' => 'Enabled',
                    '0' => 'Disabled',
                ],
                'value_class' => [
                    '1' => 'text-success',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            'username' => [
                'label' => 'username',
                'type' => 'text',
            ],
            'email' => [
                'label' => 'email',
                'type' => 'text',
                'selector' => 'tm.email',
            ],
            'enabled' => [
                'label' => 'enabled',
                'type' => 'yes_no',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->select('tm.email')
            ->from('#__user', 'tm')
            ->groupBy('tm.id')
        ;

        $this->joinNameMetadata($queryBuilder);

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareResult(array $result): array
    {
        foreach ($result as &$row) {
            $name = $row['name']
                ? sprintf('%s, [%s]', $row['name'],  $row['username'])
                : $row['username'];

            $row['username'] = sprintf(
                '<a href="%2$s" title="%1$s" class="link-title">%1$s %3$s</a>',
                $name,
                $this->router->generate('backend.user.edit', ['id' => $row['id']]),
                '<br /><span class="slug">' . $this->translator->trans('emailAddress', ['email' =>  $row['email']], 'users') . '</span>'
            );
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        $editLink = $this->router->generate('backend.user.edit', ['id' => $row['id']]);
        $delete = $this->translator->trans('deleteUser', [], 'users');

        return [
            'main' => '<a href="' . $editLink . '" class="btn btn-secondary btn-icon-only"><i class="btn-icon fas fa-pen"></i></a>',
            '<a href="" class="dropdown-item-with-icon dropdown-item-danger" title="' . $delete . '"><i class="dropdown-icon fas fa-times"></i> ' . $delete . '</a>',
        ];
    }

    private function joinNameMetadata(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->addSelect('uml.value AS name')
            ->leftJoin('tm', '#__user_metadata', 'um', "um.user_id = tm.id AND um.name = 'name'")
            ->leftJoin('um', '#__user_metadata_lang', 'uml', 'um.id = uml.metadata_id')
            ->addGroupBy('uml.value');
    }
}
