<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Cms\SearchAnything;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContactForms\Query\Enum\ScopeEnum;
use Tulia\Cms\ContactForms\Query\FinderFactoryInterface;
use Tulia\Cms\SearchAnything\Ports\Provider\AbstractProvider;
use Tulia\Cms\SearchAnything\Domain\Model\Hit;
use Tulia\Cms\SearchAnything\Domain\Model\Results;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    protected FinderFactoryInterface $finderFactory;

    protected RouterInterface $router;

    protected TranslatorInterface $translator;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->finderFactory = $finderFactory;
        $this->router = $router;
        $this->translator = $translator;
    }

    public function search(string $query, int $limit = 5, int $page = 1): Results
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::SEARCH);
        $finder->setCriteria([
            'search'   => $query,
            'per_page' => $limit,
            'page'     => $page,
            'count_found_rows' => true,
        ]);
        $finder->fetch();

        $results = new Results();

        $nodes = $finder->getResult();

        foreach ($nodes as $node) {
            $hit = new Hit($node->getName(), $this->router->generate('backend.form.edit', ['id' => $node->getId() ]));

            $results->add($node->getId(), $hit);
        }

        return $results;
    }

    public function getId(): string
    {
        return 'form';
    }

    public function getLabel(): array
    {
        return ['forms', [], 'forms'];
    }

    public function getIcon(): string
    {
        return 'fas fa-window-restore';
    }
}
