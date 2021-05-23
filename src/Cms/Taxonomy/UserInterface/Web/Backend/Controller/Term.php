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
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;
use Tulia\Cms\Taxonomy\Domain\WriteModel\TaxonomyRepository;
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

    private TaxonomyRepository $repository;

    public function __construct(
        TermFinderInterface $termFinder,
        TaxonomyRepository $repository
    ) {
        $this->termFinder = $termFinder;
        $this->repository = $repository;
    }

    public function index(string $taxonomyType): RedirectResponse
    {
        return $this->redirectToRoute('backend.term.list', ['taxonomyType' => $taxonomyType]);
    }

    /**
     * @param Request $request
     * @param string $taxonomyType
     * @return RedirectResponse|ViewInterface
     * @throws NotFoundHttpException
     */
    public function list(Request $request, string $taxonomyType)
    {
        $criteria = (new RequestCriteriaBuilder($request))->build([ 'taxonomy_type' => $taxonomyType ]);
        $terms = $this->termFinder->find($criteria, TermFinderScopeEnum::BACKEND_LISTING);

        return $this->view('@backend/taxonomy/term/list.tpl', [
            'terms'        => $terms,
            'taxonomyType' => $this->repository->getTaxonomyType($criteria['taxonomy_type']),
            'criteria'     => $criteria,
            'paginator'    => new Paginator($request, $terms->totalRows(), $request->query->getInt('page', 1), $criteria['per_page']),
        ]);
    }

    /**
     * @param Request $request
     * @param string $taxonomyType
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="term_form")
     */
    public function create(Request $request, string $taxonomyType)
    {
        $taxonomy = $this->repository->get($taxonomyType);
        $term = $this->repository->createNewTerm($taxonomy);

        $form = $this->createForm(TermForm::class, $term, ['taxonomy_type' => $taxonomy->getType()->getType()]);
        $form->handleRequest($request);

        $taxonomyTypeObject = $taxonomy->getType();

        if ($form->isSubmitted() && $form->isValid()) {
            $taxonomy->addTerm($term);
            $this->repository->save($taxonomy);

            $this->setFlash('success', $this->trans('termSaved', [], $taxonomyTypeObject->getTranslationDomain()));
            return $this->redirectToRoute('backend.term.edit', [ 'id' => $term->getId(), 'taxonomyTypeObject' => $taxonomyTypeObject->getType() ]);
        }

        return $this->view('@backend/taxonomy/term/create.tpl', [
            'taxonomyType' => $taxonomyTypeObject,
            'term' => $term,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $taxonomyType
     * @param string $id
     * @return RedirectResponse|ViewInterface
     * @throws NotFoundHttpException
     * @CsrfToken(id="term_form")
     */
    public function edit(Request $request, string $taxonomyType, string $id)
    {
        $taxonomy = $this->repository->get($taxonomyType);

        try {
            $term = $taxonomy->getTerm(new TermId($id));
        } catch (TermNotFoundException $e) {
            $this->setFlash('warning', $this->trans('termNotFound', [], 'categories'));
            return $this->redirectToRoute('backend.term.list', ['taxonomyType' => $taxonomyType]);
        }

        $form = $this->createForm(TermForm::class, $term, ['taxonomy_type' => $taxonomy->getType()->getType()]);
        $form->handleRequest($request);

        $taxonomyTypeObject = $taxonomy->getType();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save($taxonomy);

            $this->setFlash('success', $this->trans('termSaved', [], $taxonomyTypeObject->getTranslationDomain()));
            return $this->redirectToRoute('backend.term.edit', [ 'id' => $term->getId(), 'taxonomyType' => $taxonomyTypeObject->getType() ]);
        }

        return $this->view('@backend/taxonomy/term/edit.tpl', [
            'taxonomyType' => $taxonomyTypeObject,
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
        $taxonomy = $this->repository->get($request->query->get('taxonomy_type', 'category'));
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
            $this->repository->save($taxonomy);
            $this->setFlash('success', $this->trans('selectedNodesWereDeleted', [], $taxonomy->getType()->getTranslationDomain()));
        }

        return $this->redirectToRoute('backend.term', [ 'taxonomyType' => $taxonomy->getType()->getType() ]);
    }
}
