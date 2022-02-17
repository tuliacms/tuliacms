<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\SearchAnything;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderInterface;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\ContactFormFinderScopeEnum;
use Tulia\Cms\SearchAnything\Model\Hit;
use Tulia\Cms\SearchAnything\Model\Results;
use Tulia\Cms\SearchAnything\Provider\AbstractProvider;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    protected ContactFormFinderInterface $finder;

    protected RouterInterface $router;

    protected TranslatorInterface $translator;

    public function __construct(
        ContactFormFinderInterface $finder,
        RouterInterface $router,
        TranslatorInterface $translator
    ) {
        $this->finder = $finder;
        $this->router = $router;
        $this->translator = $translator;
    }

    public function search(string $query, int $limit = 5, int $page = 1): Results
    {
        $forms = $this->finder->find([
            'search'   => $query,
            'per_page' => $limit,
            'page'     => $page,
            'count_found_rows' => true,
        ], ContactFormFinderScopeEnum::SEARCH);

        $results = new Results();

        foreach ($forms as $form) {
            $hit = new Hit($form->getName(), $this->router->generate('backend.contact_form.edit', ['id' => $form->getId() ]));

            $results->add($form->getId(), $hit);
        }

        return $results;
    }

    public function getId(): string
    {
        return 'contact_form';
    }

    public function getLabel(): array
    {
        return ['forms', [], 'contact-form'];
    }

    public function getIcon(): string
    {
        return 'fas fa-window-restore';
    }
}
