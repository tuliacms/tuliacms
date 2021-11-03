<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutBuilder;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\FormService;
use Tulia\Cms\Node\Domain\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Domain\NodeType\NodeTypeRegistryInterface;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\SingularFlagImposedOnMoreThanOneNodeException;
use Tulia\Cms\Node\Domain\WriteModel\NodeRepository;
use Tulia\Cms\Node\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\NodeFinderInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\NodeForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Shared\Domain\ReadModel\Finder\Model\Collection;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface as TaxonomyRegistry;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderInterface;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    private NodeTypeRegistryInterface $typeRegistry;

    private NodeRepository $repository;

    private NodeFinderInterface $nodeFinder;

    private TaxonomyRegistry $registry;

    private TermFinderInterface $termFinder;

    private DatatableFactory $factory;

    private NodeDatatableFinderInterface $finder;

    private LayoutBuilder $layoutBuilder;
    private FormService $formService;

    public function __construct(
        NodeTypeRegistryInterface $typeRegistry,
        NodeRepository $repository,
        NodeFinderInterface $nodeFinder,
        TaxonomyRegistry $registry,
        TermFinderInterface $termFinder,
        DatatableFactory $factory,
        NodeDatatableFinderInterface $finder,
        LayoutBuilder $layoutBuilder,
        FormService $formService
    ) {
        $this->typeRegistry = $typeRegistry;
        $this->repository = $repository;
        $this->nodeFinder = $nodeFinder;
        $this->registry = $registry;
        $this->termFinder = $termFinder;
        $this->factory = $factory;
        $this->finder = $finder;
        $this->layoutBuilder = $layoutBuilder;
        $this->formService = $formService;
    }

    public function index(string $node_type): RedirectResponse
    {
        return $this->redirectToRoute('backend.node.list', ['node_type' => $node_type]);
    }

    public function list(Request $request, string $node_type): ViewInterface
    {
        $nodeTypeObject = $this->findNodeType($node_type);
        $this->finder->setNodeType($nodeTypeObject);

        return $this->view('@backend/node/list.tpl', [
            'nodeType'   => $nodeTypeObject,
            'datatable'  => $this->factory->create($this->finder, $request),
            'taxonomies' => $this->collectTaxonomies($nodeTypeObject),
        ]);
    }

    public function datatable(Request $request, string $node_type): JsonResponse
    {
        $this->finder->setNodeType($this->findNodeType($node_type));
        return $this->factory->create($this->finder, $request)->generateResponse();
    }

    /**
     * @param Request $request
     * @param string $node_type
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="content_builder_form_page")
     */
    public function create(Request $request, string $node_type)
    {
        $node = $this->repository->createNew($node_type);

        $formDescriptor = $this->formService->buildFormDescriptor(
            $node->getType()->getType(),
            $node->getId()->getId(),
            $node->getAttributes(),
            $request
        );
        $nodeType = $formDescriptor->getNodeType();

        if ($formDescriptor->isFormValid()) {
            $node->updateAttributes($formDescriptor->getData());

            $this->repository->insert($node);

            $this->setFlash('success', $this->trans('nodeSaved', [], $nodeType->getTranslationDomain()));
            return $this->redirectToRoute('backend.node.edit', [ 'id' => $node->getId(), 'node_type' => $nodeType->getType() ]);
        }

        return $this->view('@backend/node/create.tpl', [
            'nodeType' => $nodeType,
            'node'     => $node,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @param Request $request
     * @param string $node_type
     * @param string $id
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="content_builder_form_page")
     */
    public function edit(Request $request, string $node_type, string $id)
    {
        try {
            $model = $this->repository->find($id);
        } catch (NodeNotFoundException $e) {
            $this->setFlash('warning', $this->trans('nodeNotFound'));
            return $this->redirectToRoute('backend.node.list');
        }

        $nodeType = $this->typeRegistry->getType($node_type);

        $formDescriptor = $this->formService->buildFormDescriptor($nodeType->getType(), [
            'id' => $model->getId(),
            'title' => $model->getTitle(),
            'slug' => $model->getSlug(),
            'introduction' => $model->getIntroduction(),
            'content' => $model->getContent(),
            'flags' => $model->getFlags(),
        ], $request);
        $form = $formDescriptor->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                dump($form->getData());exit;
                exit;
                $this->repository->update($form->getData());
                $this->setFlash('success', $this->trans('nodeSaved', [], $nodeType->getTranslationDomain()));
                return $this->redirectToRoute('backend.node.edit', [ 'id' => $model->getId(), 'node_type' => $nodeType->getType() ]);
            } catch (SingularFlagImposedOnMoreThanOneNodeException $e) {
                $error = new FormError($this->trans('singularFlagImposedOnMoreThanOneNode', ['flag' => $e->getFlag()], $nodeType->getTranslationDomain()));
                $form->get('flags')->addError($error);
            }
        }

        return $this->view('@backend/node/edit.tpl', [
            'nodeType' => $nodeType,
            'node'     => $model,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="node.change-status")
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $nodeType = $this->findNodeType($request->query->get('node_type', 'page'));
        $status = $request->query->get('status');

        foreach ($request->request->get('ids') as $id) {
            try {
                $node = $this->repository->find($id);
            } catch (NodeNotFoundException $e) {
                continue;
            }

            switch ($status) {
                case 'trashed'  : $node->setStatus('trashed'); break;
                case 'published': $node->setStatus('published'); break;
                default         : return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getType() ]);
            }

            $this->repository->update($node);
        }

        switch ($request->query->get('status')) {
            case 'trashed'  : $message = 'selectedNodesWereTrashed'; break;
            case 'published': $message = 'selectedNodesWerePublished'; break;
            default         : $message = 'selectedNodesWereUpdated'; break;
        }

        $this->setFlash('success', $this->trans($message, [], $nodeType->getTranslationDomain()));
        return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getType() ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="node.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $nodeType = $this->findNodeType($request->query->get('node_type', 'page'));
        $removedNodes = 0;

        foreach ($request->request->get('ids') as $id) {
            try {
                $node = $this->repository->find($id);
            } catch (NodeNotFoundException $e) {
                continue;
            }

            $this->repository->delete($node);
            $removedNodes++;
        }

        if ($removedNodes) {
            $this->setFlash('success', $this->trans('selectedNodesWereDeleted', [], $nodeType->getTranslationDomain()));
        }

        return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getType() ]);
    }

    protected function findNodeType(string $type): NodeTypeInterface
    {
        $nodeType = $this->typeRegistry->getType($type);

        if (! $nodeType) {
            throw $this->createNotFoundException('Node type not found.');
        }

        return $nodeType;
    }

    private function collectTaxonomies(NodeTypeInterface $nodeType): array
    {
        $result = [];

        foreach ($nodeType->getTaxonomies() as $tax) {
            $result[] = $this->registry->getType($tax['taxonomy']);
        }

        return $result;
    }

    private function fetchNodesCategories(Collection $nodes): Collection
    {
        $idList = array_map(function ($node) {
            return $node->getCategory();
        }, iterator_to_array($nodes));
        $idList = array_filter($idList);
        $idList = array_unique($idList);

        $terms = $this->termFinder->find([
            'id__in' => $idList,
        ], TermFinderScopeEnum::INTERNAL);

        foreach ($nodes as $node) {
            if (empty($node->getCategory())) {
                continue;
            }

            foreach ($terms as $term) {
                if ($term->getId() === $node->getCategory()) {
                    $node->setMeta('__category_name', $term->getName());
                }
            }
        }

        return $nodes;
    }
}
