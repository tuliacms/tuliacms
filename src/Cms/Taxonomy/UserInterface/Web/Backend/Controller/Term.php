<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Shared\Pagination\Paginator;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\Enum\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\TaxonomyRepository;
use Tulia\Cms\Taxonomy\Domain\WriteModel\TermRepository;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermFinderInterface;
use Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\TermForm;
use Tulia\Cms\Taxonomy\UserInterface\Web\Shared\CriteriaBuilder\RequestCriteriaBuilder;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    private TermFinderInterface $termFinder;

    private TermRepository $repository;

    private TaxonomyRepository $taxonomyRepository;

    public function __construct(
        TermFinderInterface $termFinder,
        TermRepository $repository,
        TaxonomyRepository $taxonomyRepository
    ) {
        $this->termFinder = $termFinder;
        $this->repository = $repository;
        $this->taxonomyRepository = $taxonomyRepository;
    }

    public function index(string $taxonomy_type): RedirectResponse
    {
        return $this->redirectToRoute('backend.term.list', ['taxonomy_type' => $taxonomy_type]);
    }

    /**
     * @param Request $request
     * @param string $taxonomy_type
     * @return RedirectResponse|ViewInterface
     * @throws NotFoundHttpException
     */
    public function list(Request $request, string $taxonomy_type)
    {
        $criteria = (new RequestCriteriaBuilder($request))->build([ 'taxonomy_type' => $taxonomy_type ]);
        $terms = $this->termFinder->find($criteria, TermFinderScopeEnum::BACKEND_LISTING);

        return $this->view('@backend/taxonomy/term/list.tpl', [
            'terms'        => $terms,
            'taxonomyType' => $this->taxonomyRepository->getTaxonomyType($criteria['taxonomy_type']),
            'criteria'     => $criteria,
            'paginator'    => new Paginator($request, $terms->totalRows(), $request->query->getInt('page', 1), $criteria['per_page']),
        ]);
    }

    /**
     * @param Request $request
     * @param string $taxonomy_type
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="term_form")
     */
    public function create(Request $request, string $taxonomy_type)
    {
        $term = $this->taxonomyRepository->createNewTerm([
            'type' => $taxonomy_type,
            'visibility' => true,
        ]);
        $taxonomy = $this->taxonomyRepository->get($taxonomy_type);

        $form = $this->createForm(TermForm::class, $term, ['taxonomy_type' => $term->getType()]);
        $form->handleRequest($request);

        $taxonomyType = $this->taxonomyRepository->getTaxonomyType($term->getType());

        if ($form->isSubmitted() && $form->isValid()) {
            $taxonomy->addTerm($term);
            $this->taxonomyRepository->save($taxonomy);

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
     * @return RedirectResponse|ViewInterface
     * @throws NotFoundHttpException
     * @CsrfToken(id="term_form")
     */
    public function edit(Request $request, string $taxonomy_type, string $id)
    {
        try {
            $taxonomy = $this->taxonomyRepository->get($taxonomy_type);
            $term = $taxonomy->getTerm($id);
        } catch (TermNotFoundException $e) {
            $this->setFlash('warning', $this->trans('termNotFound', [], 'categories'));
            return $this->redirectToRoute('backend.term.list', ['taxonomy_type' => $taxonomy_type]);
        }

        $form = $this->createForm(TermForm::class, $term, ['taxonomy_type' => $term->getType()]);
        $form->handleRequest($request);

        $taxonomyType = $this->taxonomyRepository->getTaxonomyType($taxonomy_type);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->taxonomyRepository->save($taxonomy);

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
     * @return RedirectResponse
     * @CsrfToken(id="node.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $taxonomy = $this->taxonomyRepository->get($request->query->get('taxonomy_type', 'category'));
        $removedNodes = 0;

        foreach ($request->request->get('ids') as $id) {
            try {
                $term = $taxonomy->getTerm($id);
            } catch (TermNotFoundException $e) {
                continue;
            }

            $taxonomy->removeTerm($term);
            $removedNodes++;
        }

        if ($removedNodes) {
            $this->taxonomyRepository->save($taxonomy);
            $this->setFlash('success', $this->trans('selectedNodesWereDeleted', [], $taxonomy->getType()->getTranslationDomain()));
        }

        return $this->redirectToRoute('backend.term', [ 'taxonomy_type' => $taxonomy->getType()->getType() ]);
    }
}
