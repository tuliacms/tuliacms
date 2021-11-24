<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\TaxonomyFormService;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;
use Tulia\Cms\Taxonomy\Domain\WriteModel\TaxonomyRepository;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\Datatable\TermDatatableFinderInterface;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderInterface;
use Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\TermForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term as Model;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    private TermFinderInterface $termFinder;

    private TaxonomyRepository $repository;

    private DatatableFactory $factory;

    private TermDatatableFinderInterface $finder;
    private TaxonomyFormService $taxonomyFormService;

    public function __construct(
        TermFinderInterface $termFinder,
        TaxonomyRepository $repository,
        DatatableFactory $factory,
        TermDatatableFinderInterface $finder,
        TaxonomyFormService $taxonomyFormService
    ) {
        $this->termFinder = $termFinder;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->finder = $finder;
        $this->taxonomyFormService = $taxonomyFormService;
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
        $taxonomy = $this->repository->get($taxonomyType);
        $this->finder->setTaxonomyType($taxonomyType);

        return $this->view('@backend/taxonomy/term/list.tpl', [
            'taxonomyType' => $taxonomy->getType(),
            'datatable' => $this->factory->create($this->finder, $request),
        ]);
    }

    public function datatable(Request $request, string $taxonomyType): JsonResponse
    {
        $this->finder->setTaxonomyType($taxonomyType);
        return $this->factory->create($this->finder, $request)->generateResponse();
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

        $formDescriptor = $this->produceFormDescriptor($taxonomy, $term, $request);
        $taxonomyTypeObject = $formDescriptor->getContentType();

        if ($formDescriptor->isFormValid()) {
            $this->updateModel($formDescriptor, $term);
            $taxonomy->addTerm($term);
            $this->repository->save($taxonomy);

            $this->setFlash('success', $this->trans('termSaved', [], $taxonomyTypeObject->getTranslationDomain()));
            return $this->redirectToRoute('backend.term.edit', [ 'id' => $term->getId(), 'taxonomyType' => $taxonomyTypeObject->getType() ]);
        }

        return $this->view('@backend/taxonomy/term/create.tpl', [
            'taxonomyType' => $taxonomyTypeObject,
            'term' => $term,
            'formDescriptor' => $formDescriptor,
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
     * @CsrfToken(id="term.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $taxonomy = $this->repository->get($request->query->get('taxonomy_type', 'category'));
        $removedNodes = 0;

        foreach ($request->request->get('ids') as $id) {
            try {
                $term = $taxonomy->getTerm(new TermId($id));
            } catch (TermNotFoundException $e) {
                continue;
            }

            $taxonomy->removeTerm($term);
            $removedNodes++;
        }

        if ($removedNodes) {
            $this->repository->save($taxonomy);
            $this->setFlash('success', $this->trans('selectedTermsWereDeleted', [], $taxonomy->getType()->getTranslationDomain()));
        }

        return $this->redirectToRoute('backend.term', [ 'taxonomyType' => $taxonomy->getType()->getType() ]);
    }

    private function produceFormDescriptor(Taxonomy $taxonomy, Model $term, Request $request): ContentTypeFormDescriptor
    {
        return $this->taxonomyFormService->buildFormDescriptor(
            $taxonomy->getType()->getType(),
            array_merge(
                [
                    'title' => $term->getTitle(),
                    'slug' => $term->getSlug(),
                    'parent_id' => $term->getParentId(),
                ],
                $term->getAttributes()
            ),
            $request
        );
    }

    private function updateModel(ContentTypeFormDescriptor $formDescriptor, Model $term): void
    {
        $data = $formDescriptor->getData();

        $term->setSlug($data['slug']);
        $term->setTitle($data['title']);
        $term->setParentId($data['parent_id']);
        $term->updateAttributes($data);
    }
}
