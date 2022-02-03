<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\ContentFormService;
use Tulia\Cms\Node\Domain\ReadModel\Datatable\NodeDatatableFinderInterface;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node as ReadModel;
use Tulia\Cms\Node\Domain\WriteModel\Exception\NodeNotFoundException;
use Tulia\Cms\Node\Domain\WriteModel\Exception\SingularFlagImposedOnMoreThanOneNodeException;
use Tulia\Cms\Node\Domain\WriteModel\NodeRepository;
use Tulia\Cms\Platform\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Security\Http\Csrf\Annotation\IgnoreCsrfToken;
use Tulia\Component\Security\Http\Csrf\Exception\RequestCsrfTokenException;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Node extends AbstractController
{
    private ContentTypeRegistry $typeRegistry;

    private NodeRepository $repository;

    private DatatableFactory $factory;

    private NodeDatatableFinderInterface $finder;

    private ContentFormService $contentFormService;

    public function __construct(
        ContentTypeRegistry $typeRegistry,
        NodeRepository $repository,
        DatatableFactory $factory,
        NodeDatatableFinderInterface $finder,
        ContentFormService $contentFormService
    ) {
        $this->typeRegistry = $typeRegistry;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->finder = $finder;
        $this->contentFormService = $contentFormService;
    }

    public function index(string $node_type): RedirectResponse
    {
        return $this->redirectToRoute('backend.node.list', ['node_type' => $node_type]);
    }

    public function list(Request $request, string $node_type): ViewInterface
    {
        $nodeTypeObject = $this->findNodeType($node_type);
        $this->finder->setContentType($nodeTypeObject);

        return $this->view('@backend/node/list.tpl', [
            'nodeType'   => $nodeTypeObject,
            'datatable'  => $this->factory->create($this->finder, $request),
            'taxonomies' => $this->collectTaxonomies($nodeTypeObject),
        ]);
    }

    public function datatable(Request $request, string $node_type): JsonResponse
    {
        $this->finder->setContentType($this->findNodeType($node_type));
        return $this->factory->create($this->finder, $request)->generateResponse();
    }

    /**
     * @param Request $request
     * @param string $node_type
     * @return RedirectResponse|ViewInterface
     * @throws RequestCsrfTokenException
     * @IgnoreCsrfToken()
     */
    public function create(Request $request, string $node_type)
    {
        $this->validateCsrfToken($request, $node_type);

        $node = $this->repository->createNew($node_type);

        $formDescriptor = $this->produceFormDescriptor($node, $request);
        $nodeType = $formDescriptor->getContentType();

        if ($formDescriptor->isFormValid()) {
            $this->updateModel($formDescriptor, $node, 'create');

            $this->setFlash('success', $this->trans('nodeSaved', [], 'node'));
            return $this->redirectToRoute('backend.node.edit', [ 'id' => $node->getId(), 'node_type' => $nodeType->getCode() ]);
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
     * @throws RequestCsrfTokenException
     * @IgnoreCsrfToken()
     */
    public function edit(Request $request, string $node_type, string $id)
    {
        $this->validateCsrfToken($request, $node_type);

        try {
            $node = $this->repository->find($id);

            if ($node->getType() !== $node_type) {
                throw new NodeNotFoundException();
            }
        } catch (NodeNotFoundException|NodeTypeNotExistsException $e) {
            $this->setFlash('warning', $this->trans('nodeNotFound'));
            return $this->redirectToRoute('backend.node.list');
        }

        $formDescriptor = $this->produceFormDescriptor($node, $request);
        $form = $formDescriptor->getForm();
        $nodeType = $formDescriptor->getContentType();

        if ($formDescriptor->isFormValid()) {
            try {
                $this->updateModel($formDescriptor, $node, 'update');
                $this->setFlash('success', $this->trans('nodeSaved', [], 'node'));
                return $this->redirectToRoute('backend.node.edit', [ 'id' => $node->getId(), 'node_type' => $nodeType->getCode() ]);
            } catch (SingularFlagImposedOnMoreThanOneNodeException $e) {
                $error = new FormError($this->trans('singularFlagImposedOnMoreThanOneNode', ['flag' => $e->getFlag()], 'node'));
                $form->get('flags')->addError($error);
            }
        }

        return $this->view('@backend/node/edit.tpl', [
            'nodeType' => $nodeType,
            'node'     => $node,
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
                default         : return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getCode() ]);
            }

            $this->repository->update($node);
        }

        switch ($request->query->get('status')) {
            case 'trashed'  : $message = 'selectedNodesWereTrashed'; break;
            case 'published': $message = 'selectedNodesWerePublished'; break;
            default         : $message = 'selectedNodesWereUpdated'; break;
        }

        $this->setFlash('success', $this->trans($message, [], 'node'));
        return $this->redirectToRoute('backend.node', [ 'node_type' => $nodeType->getCode() ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="node.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
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
            $this->setFlash('success', $this->trans('selectedElementsWereDeleted'));
        }

        return $this->redirectToRoute('backend.node', [ 'node_type' => $request->query->get('node_type', 'page') ]);
    }

    protected function findNodeType(string $type): ContentType
    {
        $contentType = $this->typeRegistry->get($type);

        if (! $contentType || $contentType->isType('node') === false) {
            throw $this->createNotFoundException('Node type not found.');
        }

        return $contentType;
    }

    private function collectTaxonomies(ContentType $nodeType): array
    {
        $result = [];

        foreach ($nodeType->getFields() as $field) {
            if ($field->getType() !== 'taxonomy') {
                continue;
            }

            $result[] = $this->typeRegistry->get($field->getTaxonomy());
        }

        return $result;
    }

    private function produceFormDescriptor(ReadModel $node, Request $request): ContentTypeFormDescriptor
    {
        return $this->contentFormService->buildFormDescriptor(
            $node->getType(),
            array_merge(
                [
                    'title' => $node->getTitle(),
                    'slug' => $node->getSlug(),
                    'published_at' => $node->getPublishedAt(),
                    'published_to' => $node->getPublishedTo(),
                    'parent_id' => $node->getParentId(),
                    'status' => $node->getStatus(),
                    'author_id' => $node->getAuthorId(),
                ],
                $node->getAttributes()
            ),
            $request
        );
    }

    private function updateModel(ContentTypeFormDescriptor $formDescriptor, ReadModel $node, string $strategy): void
    {
        $data = $formDescriptor->getData();

        $node->setStatus($data['status']);
        $node->setSlug($data['slug'] ?? null);
        $node->setTitle($data['title']);
        $node->setPublishedAt(new ImmutableDateTime($data['published_at']));
        $node->setPublishedTo($data['published_to'] ? new ImmutableDateTime($data['published_to']) : null);
        $node->setParentId($data['parent_id'] ?? null);
        $node->setAuthorId($data['author_id']);
        $node->updateAttributes($data);

        if ($strategy === 'create') {
            $this->repository->insert($node);
        } else {
            $this->repository->update($node);
        }
    }

    /**
     * @throws RequestCsrfTokenException
     */
    private function validateCsrfToken(Request $request, string $node_type): void
    {
        /**
         * We must detect token validness manually, cause form name changes for every node type.
         */
        if ($request->isMethod('POST')) {
            $tokenId = 'content_builder_form_' . $node_type;
            $csrfToken = $request->request->all()[$tokenId]['_token'] ?? '';

            if ($this->isCsrfTokenValid($tokenId, $csrfToken) === false) {
                throw new RequestCsrfTokenException('CSRF token is invalid. Operation stopped.');
            }
        }
    }
}
