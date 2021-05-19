<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Application\Exception\TranslatableTermException;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\TermRepository;
use Tulia\Cms\Taxonomy\Query\CriteriaBuilder\RequestCriteriaBuilder;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\Exception\MultipleFetchException;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;
use Tulia\Cms\Taxonomy\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Taxonomy\Query\Factory\TermFactoryInterface;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term as QueryTerm;
use Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\TermForm;
use Tulia\Component\Templating\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    private RegistryInterface $taxonomyRegistry;

    private FinderFactoryInterface $finderFactory;

    private TermRepository $repository;

    public function __construct(
        RegistryInterface $taxonomyRegistry,
        FinderFactoryInterface $finderFactory,
        TermRepository $repository
    ) {
        $this->taxonomyRegistry = $taxonomyRegistry;
        $this->finderFactory = $finderFactory;
        $this->repository = $repository;
    }

    public function index(string $taxonomy_type): RedirectResponse
    {
        return $this->redirectToRoute('backend.term.list', ['taxonomy_type' => $taxonomy_type]);
    }

    /**
     * @param Request $request
     * @param string $taxonomy_type
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    public function list(Request $request, string $taxonomy_type)
    {
        $criteria = (new RequestCriteriaBuilder($request))->build([ 'taxonomy_type' => $taxonomy_type ]);
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_LISTING);
        $finder->setCriteria($criteria);
        $finder->fetchRaw();

        return $this->view('@backend/taxonomy/term/list.tpl', [
            'terms'        => $finder->getResult(),
            'taxonomyType' => $this->findTaxonomyType($criteria['taxonomy_type']),
            'criteria'     => $criteria,
            'paginator'    => $finder->getPaginator($request),
        ]);
    }

    /**
     * @param Request $request
     * @param string $taxonomy_type
     * @param TermFactoryInterface $termFactory
     *
     * @return RedirectResponse|ViewInterface
     *
     * @CsrfToken(id="term_form")
     */
    public function create(Request $request, string $taxonomy_type)
    {
        $term = $this->repository->createNew([
            'type' => $taxonomy_type,
            'visibility' => true,
        ]);

        $form = $this->createForm(TermForm::class, $term, ['taxonomy_type' => $term->getType()]);
        $form->handleRequest($request);

        $taxonomyType = $this->findTaxonomyType($term->getType());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->insert($term);

            $this->setFlash('success', $this->trans('termSaved', [], $taxonomyType->getTranslationDomain()));
            return $this->redirectToRoute('backend.term.edit', [ 'id' => $term->getId(), 'taxonomy_type' => $taxonomyType->getType() ]);
        }

        return $this->view('@backend/taxonomy/term/create.tpl', [
            'taxonomyType' => $taxonomyType,
            'term' => $term,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $taxonomy_type
     * @param string $id
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     *
     * @CsrfToken(id="term_form")
     */
    public function edit(Request $request, string $taxonomy_type, string $id)
    {
        try {
            $term = $this->repository->find($id);
        } catch (TermNotFoundException $e) {
            $this->setFlash('warning', $this->trans('termNotFound', [], 'categories'));
            return $this->redirectToRoute('backend.node.list');
        }

        $form = $this->createForm(TermForm::class, $term, ['taxonomy_type' => $term->getType()]);
        $form->handleRequest($request);

        $taxonomyType = $this->findTaxonomyType($taxonomy_type);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->update($term);

            $this->setFlash('success', $this->trans('termSaved', [], $taxonomyType->getTranslationDomain()));
            return $this->redirectToRoute('backend.term.edit', [ 'id' => $term->getId(), 'taxonomy_type' => $taxonomyType->getType() ]);
        }

        return $this->view('@backend/taxonomy/term/edit.tpl', [
            'taxonomyType' => $taxonomyType,
            'term' => $term,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     *
     * @CsrfToken(id="node.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $taxonomyType = $this->findTaxonomyType($request->query->get('taxonomy_type', 'categiry'));
        $removedNodes = 0;

        foreach ($request->request->get('ids') as $id) {
            try {
                $term = $this->repository->find($id);
            } catch (NotFoundHttpException $e) {
                continue;
            }

            try {
                $this->repository->delete($term);
                $removedNodes++;
            } catch (TranslatableTermException $e) {
                $this->setFlash('warning', $this->transObject($e));
            }
        }

        if ($removedNodes) {
            $this->setFlash('success', $this->trans('selectedNodesWereDeleted', [], $taxonomyType->getTranslationDomain()));
        }

        return $this->redirectToRoute('backend.term', [ 'taxonomy_type' => $taxonomyType->getType() ]);
    }

    /**
     * @param string $type
     *
     * @return TaxonomyTypeInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findTaxonomyType(string $type): TaxonomyTypeInterface
    {
        $taxonomyType = $this->taxonomyRegistry->getType($type);

        if (! $taxonomyType) {
            throw $this->createNotFoundException('Taxonomy type not found.');
        }

        return $taxonomyType;
    }

    /**
     * @param string $id
     *
     * @return QueryTerm
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getTermById(string $id): QueryTerm
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria(['id' => $id]);
        $finder->fetchRaw();

        $term = $finder->getResult()->first();

        if (! $term) {
            throw $this->createNotFoundException($this->trans('termNotFound'));
        }

        return $term;
    }
}
