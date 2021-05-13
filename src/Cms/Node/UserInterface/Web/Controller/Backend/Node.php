<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Node\Application\Command\NodeStorage;
use Tulia\Cms\Node\Application\Model\Node as ApplicationNode;
use Tulia\Cms\Node\Application\Exception\TranslatableNodeException;
use Tulia\Cms\Node\Query\Factory\NodeFactoryInterface;
use Tulia\Cms\Node\Query\Model\Collection;
use Tulia\Cms\Node\Query\Model\Node as QueryNode;
use Tulia\Cms\Node\Query\CriteriaBuilder\RequestCriteriaBuilder;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\Exception\MultipleFetchException;
use Tulia\Cms\Node\Query\Exception\QueryException;
use Tulia\Cms\Node\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\UserInterface\Web\Form\NodeForm;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface as TaxonomyRegistry;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface as TaxonomyFinderFactory;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    private FinderFactoryInterface $finderFactory;

    private NodeStorage $nodeStorage;

    private RegistryInterface $typeRegistry;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        NodeStorage $nodeStorage,
        RegistryInterface $typeRegistry
    ) {
        $this->finderFactory = $finderFactory;
        $this->nodeStorage = $nodeStorage;
        $this->typeRegistry = $typeRegistry;
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
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
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
     * @param NodeFactoryInterface $nodeFactory
     *
     * @return RedirectResponse|ViewInterface
     *
     * @CsrfToken(id="node_form")
     */
    public function create(Request $request, string $node_type, NodeFactoryInterface $nodeFactory)
    {
        $node = $nodeFactory->createNew([
            'type' => $node_type,
        ]);
        $model = ApplicationNode::fromQueryModel($node);

        $form = $this->createForm(NodeForm::class, $model, ['node_type' => $node_type]);
        $form->handleRequest($request);

        $nodeType = $this->typeRegistry->getType($node_type);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->nodeStorage->save($form->getData());

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
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     *
     * @CsrfToken(id="node_form")
     */
    public function edit(Request $request, string $node_type, string $id)
    {
        $node = $this->getNodeById($id);
        $model = ApplicationNode::fromQueryModel($node);

        $form = $this->createForm(NodeForm::class, $model, ['node_type' => $node_type]);
        $form->handleRequest($request);

        $nodeType = $this->typeRegistry->getType($node_type);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->nodeStorage->save($form->getData());

            $this->setFlash('success', $this->trans('nodeSaved', [], $nodeType->getTranslationDomain()));
            return $this->redirectToRoute('backend.node.edit', [ 'id' => $node->getId(), 'node_type' => $nodeType->getType() ]);
        }

        return $this->view('@backend/node/edit.tpl', [
            'nodeType' => $nodeType,
            'node'     => $node,
            'form'     => $form->createView(),
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
     * @CsrfToken(id="node.change-status")
     */
    public function changeStatus(Request $request): RedirectResponse
    {
        $nodeType = $this->findNodeType($request->query->get('node_type', 'page'));

        foreach ($request->request->get('ids') as $id) {
            try {
                $node = $this->getNodeById($id);
            } catch (NotFoundHttpException $e) {
                continue;
            }

            switch ($request->query->get('status')) {
                case 'trashed'  : $node->setStatus('trashed'); break;
                case 'published': $node->setStatus('published'); break;
                default         : return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getType() ]);
            }

            $this->nodeStorage->save(ApplicationNode::fromQueryModel($node));
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
        $nodeType = $this->findNodeType($request->query->get('node_type', 'page'));
        $removedNodes = 0;

        foreach ($request->request->get('ids') as $id) {
            try {
                $node = $this->getNodeById($id);
            } catch (NotFoundHttpException $e) {
                continue;
            }

            try {
                $this->nodeStorage->delete(ApplicationNode::fromQueryModel($node));
                $removedNodes++;
            } catch (TranslatableNodeException $e) {
                $this->setFlash('warning', $this->transObject($e));
            }
        }

        if ($removedNodes) {
            $this->setFlash('success', $this->trans('selectedNodesWereDeleted', [], $nodeType->getTranslationDomain()));
        }

        return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getType() ]);
    }

    /**
     * @param string $type
     *
     * @return NodeTypeInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findNodeType(string $type): NodeTypeInterface
    {
        $nodeType = $this->typeRegistry->getType($type);

        if (! $nodeType) {
            throw $this->createNotFoundException('Node type not found.');
        }

        return $nodeType;
    }

    /**
     * @param string $id
     *
     * @return QueryNode
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getNodeById(string $id): QueryNode
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria(['id' => $id]);
        $finder->fetchRaw();

        $node = $finder->getResult()->first();

        if (! $node) {
            throw $this->createNotFoundException($this->trans('nodeNotFound'));
        }

        return $node;
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
