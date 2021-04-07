<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UI\Web\Controller\Backend;

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
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface as TaxonomyRegistry;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface as TaxonomyFinderFactory;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Node\UI\Web\Form\NodeFormManagerFactory;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Framework\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    /**
     * @var RegistryInterface
     */
    protected $nodeRegistry;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var NodeStorage
     */
    protected $nodeStorage;

    /**
     * @param RegistryInterface $nodeRegistry
     * @param FinderFactoryInterface $finderFactory
     * @param NodeStorage $nodeStorage
     */
    public function __construct(
        RegistryInterface $nodeRegistry,
        FinderFactoryInterface $finderFactory,
        NodeStorage $nodeStorage
    ) {
        $this->nodeRegistry  = $nodeRegistry;
        $this->finderFactory = $finderFactory;
        $this->nodeStorage   = $nodeStorage;
    }

    /**
     * @param Request $request
     * @param TaxonomyRegistry $registry
     * @param TaxonomyFinderFactory $taxonomyFinder
     * @param string $nodeType
     * @param string|null $list
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
        string $nodeType,
        string $list = null
    ) {
        if ($list !== 'list') {
            return $this->redirect(
                'backend.node',
                array_merge(
                    ['list' => 'list', 'node_type' => $nodeType],
                    $request->query->all()
                )
            );
        }

        $criteria = (new RequestCriteriaBuilder($request))->build([ 'node_type' => $nodeType ]);
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_LISTING);
        $finder->setCriteria($criteria);
        $finder->fetchRaw();

        $nodeTypeObject = $this->findNodeType($nodeType);
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
     * @param string $nodeType
     * @param NodeFormManagerFactory $formFactory
     * @param NodeFactoryInterface $nodeFactory
     *
     * @return RedirectResponse|ViewInterface
     *
     * @CsrfToken(id="node_form")
     */
    public function create(
        Request $request,
        string $nodeType,
        NodeFormManagerFactory $formFactory,
        NodeFactoryInterface $nodeFactory
    ) {
        $node = $nodeFactory->createNew([
            'type' => $nodeType,
        ]);
        $manager = $formFactory->create($nodeType, $node);
        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('nodeSaved', [], $manager->getNodeType()->getTranslationDomain()));
            return $this->redirect('backend.node.edit', [ 'id' => $node->getId(), 'node_type' => $manager->getNodeType()->getType() ]);
        }

        return $this->view('@backend/node/create.tpl', [
            'manager'  => $manager->getManager(),
            'nodeType' => $manager->getNodeType(),
            'node'     => $node,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param NodeFormManagerFactory $factory
     * @param string $nodeType
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
    public function edit(
        Request $request,
        NodeFormManagerFactory $factory,
        string $nodeType,
        string $id
    ) {
        $node = $this->getNodeById($id);
        $manager = $factory->create($nodeType, $node);
        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('nodeSaved', [], $manager->getNodeType()->getTranslationDomain()));
            return $this->redirect('backend.node.edit', [ 'id' => $node->getId(), 'node_type' => $manager->getNodeType()->getType() ]);
        }

        return $this->view('@backend/node/edit.tpl', [
            'manager'  => $manager->getManager(),
            'nodeType' => $manager->getNodeType(),
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
                default         : return $this->redirect('backend.node', [ 'node_type' => $nodeType->getType() ]);
            }

            $this->nodeStorage->save(ApplicationNode::fromQueryModel($node));
        }

        switch ($request->query->get('status')) {
            case 'trashed'  : $message = 'selectedNodesWereTrashed'; break;
            case 'published': $message = 'selectedNodesWerePublished'; break;
            default         : $message = 'selectedNodesWereUpdated'; break;
        }

        $this->setFlash('success', $this->trans($message, [], $nodeType->getTranslationDomain()));
        return $this->redirect('backend.node', [ 'node_type' => $nodeType->getType() ]);
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

        return $this->redirect('backend.node', [ 'node_type' => $nodeType->getType() ]);
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
        $nodeType = $this->nodeRegistry->getType($type);

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
