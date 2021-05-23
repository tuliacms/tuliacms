<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Persistence\Domain\ReadModel\Datatable;

use PDO;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\Query\QueryBuilder;
use Tulia\Component\Datatable\Finder\AbstractDatatableFinder;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalNodeDatatableFinder extends AbstractDatatableFinder implements NodeDatatableFinderInterface
{
    private RouterInterface $router;

    private TranslatorInterface $translator;

    private CsrfTokenManagerInterface $csrfTokenManager;

    private ?string $nodeType = null;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite,
        RouterInterface $router,
        TranslatorInterface $translator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        parent::__construct($connection, $currentWebsite);

        $this->router = $router;
        $this->translator = $translator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function setNodeType(string $nodeType): void
    {
        $this->nodeType = $nodeType;
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
            'title' => [
                'selector' => 'COALESCE(tl.title, tm.title)',
                'label' => 'title',
                'html_attr' => ['class' => 'col-title'],
                'view' => '@backend/node/parts/datatable/title.tpl',
            ],
            /*'visibility' => [
                'selector' => 'COALESCE(tl.visibility, tm.visibility)',
                'label' => 'visibility',
                'value_translation' => [
                    '1' => $this->translator->trans('visible'),
                    '0' => $this->translator->trans('invisible'),
                ],
                'value_class' => [
                    '1' => 'text-success',
                    '0' => 'text-danger',
                ],
            ],*/
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilder
            ->from('#__node', 'tm')
            ->addSelect('tm.type, tm.level, tm.parent_id, tm.slug')
            ->leftJoin('tm', '#__node_lang', 'tl', 'tm.id = tl.node_id AND tl.locale = :locale')
            ->where('tm.type = :type')
            ->setParameter('type', $this->nodeType, PDO::PARAM_STR)
            ->setParameter('locale', $this->currentWebsite->getLocale()->getCode(), PDO::PARAM_STR)
            ->addOrderBy('tm.level', 'ASC')
        ;

        if ($this->currentWebsite->getDefaultLocale()->getCode() !== $this->currentWebsite->getLocale()->getCode()) {
            $queryBuilder->addSelect('IF(ISNULL(tl.title), 0, 1) AS translated');
        }

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildActions(array $row): array
    {
        $editLink = $this->router->generate('backend.node.edit', ['node_type' => $row['type'], 'id' => $row['id']]);
        $deleteLink = $this->router->generate('backend.node.delete', ['node_type' => $row['type']]);
        $deleteCsrfToken = $this->csrfTokenManager->getToken('node.delete');
        $delete = $this->translator->trans('deleteNode', [], 'pages');

        return [
            'main' => '<a href="' . $editLink . '" class="btn btn-secondary btn-icon-only"><i class="btn-icon fas fa-pen"></i></a>',
            '<a
                href="#"
                class="dropdown-item-with-icon dropdown-item-danger"
                title="' . $delete . '"
                data-component="action"
                data-settings="{
                    \'action\': \'delete\',
                    \'url\': \'' . $deleteLink . '\',
                    \'data\': {
                        \'ids\': [\'' . $row['id'] . '\']
                    },
                    \'csrf_token\': \'' . $deleteCsrfToken->getValue() . '\'
                }"
            ><i class="dropdown-icon fas fa-times"></i> ' . $delete . '</a>',
        ];
    }
}
