<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\ContentType\ContentTypeRepository;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\Configuration;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeBuilderRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType\FormDataToModelTransformer;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType\FormHandler;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType\ModelToFormDataTransformer;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentModel extends AbstractController
{
    private FormDataToModelTransformer $formDataToModelTransformer;
    private ContentTypeRepository $contentTypeRepository;
    private ContentTypeRegistry $contentTypeRegistry;
    private Configuration $configuration;
    private LayoutTypeBuilderRegistry $layoutTypeBuilderRegistry;

    public function __construct(
        FormDataToModelTransformer $formDataToModelTransformer,
        ContentTypeRepository $contentTypeRepository,
        ContentTypeRegistry $contentTypeRegistry,
        Configuration $configuration,
        LayoutTypeBuilderRegistry $layoutTypeBuilderRegistry
    ) {
        $this->formDataToModelTransformer = $formDataToModelTransformer;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->configuration = $configuration;
        $this->layoutTypeBuilderRegistry = $layoutTypeBuilderRegistry;
    }

    public function index(): ViewInterface
    {
        return $this->view('@backend/content_builder/index.tpl', [
            'contentTypeList' => $this->contentTypeRegistry->all(),
            'contentTypeCodes' => $this->configuration->getTypes(),
        ]);
    }

    /**
     * @CsrfToken(id="create-content-type")
     * @return ViewInterface|RedirectResponse
     */
    public function create(string $contentType, Request $request, FormHandler $nodeTypeFormHandler)
    {
        if ($this->configuration->typeExists($contentType) === false) {
            $this->addFlash('danger', $this->trans('contentTypeOfNotExists', ['name' => $contentType], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        if ($request->isMethod('POST')) {
            $data = json_decode($request->request->get('node_type'), true);
        } else {
            $data = [];
        }

        $data = $nodeTypeFormHandler->handle($request, $data);

        if ($nodeTypeFormHandler->isRequestValid()) {
            $layoutType = $this->formDataToModelTransformer->produceLayoutType($data);
            $nodeType = $this->formDataToModelTransformer->produceContentType($data, $contentType, $layoutType);

            try {
                $this->contentTypeRepository->insert($nodeType);
            } catch (\Exception $e) {
                dump($e);exit;
            }

            $this->setFlash('success', $this->trans('contentTypeCreatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $layoutBuilder = $this->layoutTypeBuilderRegistry->get($this->configuration->getLayoutBuilder($contentType));

        return $this->view('@backend/content_builder/content_type/create.tpl', [
            'type' => $contentType,
            'builderView' => $layoutBuilder->builderView($contentType, $data, $nodeTypeFormHandler->getErrors(), true),
            'cleaningResult' => $nodeTypeFormHandler->getCleaningResult(),
        ]);
    }

    /**
     * @CsrfToken(id="create-content-type")
     * @return ViewInterface|RedirectResponse
     */
    public function edit(
        string $id,
        string $contentType,
        Request $request,
        FormHandler $nodeTypeFormHandler
    ) {
        if ($this->configuration->typeExists($contentType) === false) {
            $this->addFlash('danger', $this->trans('contentTypeOfNotExists', ['name' => $contentType], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $contentType = $this->contentTypeRepository->find($id);

        if ($contentType === null) {
            $this->setFlash('danger', $this->trans('contentTypeNotExists', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        if ($contentType->isInternal()) {
            $this->setFlash('danger', $this->trans('cannotEditInternalContentType', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $layout = $contentType->getLayout();

        $data = (new ModelToFormDataTransformer())->transform($contentType, $layout);
        $data = $nodeTypeFormHandler->handle($request, $data, true);

        if ($nodeTypeFormHandler->isRequestValid()) {
            $layoutType = $this->formDataToModelTransformer->produceLayoutType($data);
            $nodeType = $this->formDataToModelTransformer->produceContentType($data, $contentType->getType(), $layoutType);

            try {
                $this->contentTypeRepository->update($nodeType);
            } catch (\Exception $e) {
                dump($e);exit;
            }

            $this->setFlash('success', $this->trans('contentTypeUpdatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $layoutBuilder = $this->layoutTypeBuilderRegistry->get($this->configuration->getLayoutBuilder($contentType->getType()));

        return $this->view('@backend/content_builder/content_type/edit.tpl', [
            'type' => $contentType->getType(),
            'builderView' => $layoutBuilder->builderView($contentType->getType(), $data, $nodeTypeFormHandler->getErrors(), false),
            'cleaningResult' => $nodeTypeFormHandler->getCleaningResult(),
        ]);
    }

    /**
     * @CsrfToken(id="delete-content-type")
     */
    public function delete(string $id): RedirectResponse
    {
        $contentType = $this->contentTypeRepository->find($id);

        if ($contentType === null) {
            $this->setFlash('danger', $this->trans('contentTypeNotExists', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        if ($contentType->isInternal()) {
            $this->setFlash('danger', $this->trans('cannotRemoveInternalContentType', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $this->contentTypeRepository->delete($contentType);

        $this->setFlash('success', $this->trans('contentTypeWasRemoved', [], 'content_builder'));
        return $this->redirectToRoute('backend.content_builder.homepage');
    }
}
