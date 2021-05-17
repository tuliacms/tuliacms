<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\WriteModel\NodeRepository;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\CriteriaBuilder\RequestCriteriaBuilder;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Query\Model\Collection;
use Tulia\Cms\Node\UserInterface\Web\Form\NodeForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface as TaxonomyRegistry;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface as TaxonomyFinderFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    private FinderFactoryInterface $finderFactory;

    private RegistryInterface $typeRegistry;

    private NodeRepository $repository;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        RegistryInterface $typeRegistry,
        NodeRepository $repository
    ) {
        $this->finderFactory = $finderFactory;
        $this->typeRegistry = $typeRegistry;
        $this->repository = $repository;
    }

    public function index(string $node_type): RedirectResponse
    {
        return $this->redirectToRoute('backend.node.list', ['node_type' => $node_type]);
    }

    /**
     * @param Request $request
     * @param TaxonomyRegistry $registry
     * @param TaxonomyFinderFactory $taxonomyFinder
     * @param string $node_type
     * @return RedirectResponse|ViewInterface
     */
    public function list(
        Request $request,
        TaxonomyRegistry $registry,
        TaxonomyFinderFactory $taxonomyFinder,
        string $node_type
    ) {
        $criteria = (new RequestCriteriaBuilder($request))->build([ 'node_type' => $node_type ]);
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_LISTING);
        $finder->setCriteria($criteria);
        $finder->fetchRaw();

        $nodeTypeObject = $this->findNodeType($node_type);
        $nodes = $this->fetchNodesCategories($taxonomyFinder, $finder->getResult());

        return $this->view('@backend/node/list.tpl', [
            'nodes'      => $nodes,
            'nodeType'   => $nodeTypeObject,
            'criteria'   => $criteria,
            'paginator'  => $finder->getPaginator($request),
            'taxonomies' => $this->collectTaxonomies($registry, $nodeTypeObject),
        ]);
    }

    /**
     * @param Request $request
     * @param string $node_type
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="node_form")
     */
    public function create(Request $request, string $node_type)
    {
        $node = $this->repository->createNew(['type' => $node_type]);

        $form = $this->createForm(NodeForm::class, $node, ['node_type' => $node_type]);
        $form->handleRequest($request);

        $nodeType = $this->typeRegistry->getType($node_type);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->insert($form->getData());

            $this->setFlash('success', $this->trans('nodeSaved', [], $nodeType->getTranslationDomain()));
            return $this->redirectToRoute('backend.node.edit', [ 'id' => $node->getId(), 'node_type' => $nodeType->getType() ]);
        }

        return $this->view('@backend/node/create.tpl', [
            'nodeType' => $nodeType,
            'node'     => $node,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param string $node_type
     * @param string $id
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="node_form")
     */
    public function edit(Request $request, string $node_type, string $id)
    {
        try {
            $model = $this->repository->find($id);
        } catch (NodeNotFoundException $e) {
            $this->setFlash('warning', $this->trans('nodeNotFound'));
            return $this->redirectToRoute('backend.node.list');
        }

        $form = $this->createForm(NodeForm::class, $model, ['node_type' => $node_type]);
        $form->handleRequest($request);

        $nodeType = $this->typeRegistry->getType($node_type);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->update($form->getData());

            $this->setFlash('success', $this->trans('nodeSaved', [], $nodeType->getTranslationDomain()));
            return $this->redirectToRoute('backend.node.edit', [ 'id' => $model->getId(), 'node_type' => $nodeType->getType() ]);
        }

        return $this->view('@backend/node/edit.tpl', [
            'nodeType' => $nodeType,
            'node'     => $model,
            'form'     => $form->createView(),
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

    private function collectTaxonomies(TaxonomyRegistry $registry, NodeTypeInterface $nodeType): array
    {
        $result = [];

        foreach ($nodeType->getTaxonomies() as $tax) {
            $result[] = $registry->getType($tax['taxonomy']);
        }

        return $result;
    }

    private function fetchNodesCategories(TaxonomyFinderFactory $taxonomyFinder, Collection $nodes): Collection
    {
        $idList = array_map(function ($node) {
            return $node->getCategory();
        }, iterator_to_array($nodes));
        $idList = array_filter($idList);
        $idList = array_unique($idList);

        $finder = $taxonomyFinder->getInstance(\Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum::INTERNAL);
        $finder->setCriteria([
            'id__in' => $idList,
        ]);
        $finder->fetchRaw();
        $terms = $finder->getResult();

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
