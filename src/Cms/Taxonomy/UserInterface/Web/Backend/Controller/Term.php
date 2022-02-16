<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\ContentFormService;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\Datatable\TermDatatableFinderInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Exception\TermNotFoundException;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Taxonomy;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term as Model;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\ValueObject\TermId;
use Tulia\Cms\Taxonomy\Domain\WriteModel\TaxonomyRepository;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    private TermFinderInterface $termFinder;
    private TaxonomyRepository $repository;
    private DatatableFactory $factory;
    private TermDatatableFinderInterface $finder;
    private ContentFormService $contentFormService;
    private ContentTypeRegistryInterface $typeRegistry;

    public function __construct(
        TermFinderInterface $termFinder,
        TaxonomyRepository $repository,
        DatatableFactory $factory,
        TermDatatableFinderInterface $finder,
        ContentFormService $contentFormService,
        ContentTypeRegistryInterface $typeRegistry
    ) {
        $this->termFinder = $termFinder;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->finder = $finder;
        $this->contentFormService = $contentFormService;
        $this->typeRegistry = $typeRegistry;
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
            'taxonomyType' => $this->typeRegistry->get($taxonomy->getType()),
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
     * @CsrfToken(id="content_builder_form_category")
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

            $this->setFlash('success', $this->trans('termSaved', [], 'taxonomy'));
            return $this->redirectToRoute('backend.term.edit', [ 'id' => $term->getId(), 'taxonomyType' => $taxonomyTypeObject->getCode() ]);
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
     * @CsrfToken(id="content_builder_form_category")
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

        $formDescriptor = $this->produceFormDescriptor($taxonomy, $term, $request);
        $taxonomyTypeObject = $formDescriptor->getContentType();

        if ($formDescriptor->isFormValid()) {
            $this->updateModel($formDescriptor, $term);
            $this->repository->save($taxonomy);

            $this->setFlash('success', $this->trans('termSaved', [], 'taxonomy'));
            return $this->redirectToRoute('backend.term.edit', [ 'id' => $term->getId(), 'taxonomyType' => $taxonomyTypeObject->getCode() ]);
        }

        return $this->view('@backend/taxonomy/term/edit.tpl', [
            'taxonomyType' => $taxonomyTypeObject,
            'term' => $term,
            'formDescriptor' => $formDescriptor,
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
            $this->setFlash('success', $this->trans('selectedTermsWereDeleted', [], 'taxonomy'));
        }

        return $this->redirectToRoute('backend.term', [ 'taxonomyType' => $taxonomy->getType()->getType() ]);
    }

    private function produceFormDescriptor(Taxonomy $taxonomy, Model $term, Request $request): ContentTypeFormDescriptor
    {
        return $this->contentFormService->buildFormDescriptor(
            $taxonomy->getType(),
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
        $term->setParentId($data['parent_id'] ? new TermId($data['parent_id']) : null);
        $term->updateAttributes($data);
    }
}
