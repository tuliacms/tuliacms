<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\ContentType\ContentTypeRepository;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Routing\Strategy\ContentTypeRoutingStrategyRegistry;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\Configuration;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
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
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private FormDataToModelTransformer $formDataToModelTransformer;
    private ContentTypeRepository $contentTypeRepository;
    private ContentTypeRegistry $contentTypeRegistry;
    private ContentTypeRoutingStrategyRegistry $strategyRegistry;
    private Configuration $configuration;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        FormDataToModelTransformer $formDataToModelTransformer,
        ContentTypeRepository $contentTypeRepository,
        ContentTypeRegistry $contentTypeRegistry,
        ContentTypeRoutingStrategyRegistry $strategyRegistry,
        Configuration $configuration
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->formDataToModelTransformer = $formDataToModelTransformer;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->strategyRegistry = $strategyRegistry;
        $this->configuration = $configuration;
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

            }

            $this->setFlash('success', $this->trans('nodeTypeCreatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        return $this->view('@backend/content_builder/content_type/create.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'routingStrategies' => $this->getRoutingStrategies($contentType),
            'model' => $data,
            'errors' => $nodeTypeFormHandler->getErrors(),
            'cleaningResult' => $nodeTypeFormHandler->getCleaningResult(),
        ]);
    }

    /**
     * @CsrfToken(id="create-content-type")
     * @return ViewInterface|RedirectResponse
     */
    public function edit(string $code, string $contentType, Request $request, FormHandler $nodeTypeFormHandler)
    {
        if ($this->contentTypeRegistry->has($code) === false) {
            $this->setFlash('danger', $this->trans('nodeTypeNotExists', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $type = $this->contentTypeRegistry->get($code);

        if ($type->isInternal()) {
            $this->setFlash('danger', $this->trans('cannotEditInternalNodeType', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $layout = $type->getLayout();

        $data = (new ModelToFormDataTransformer())->transform($type, $layout);
        $data = $nodeTypeFormHandler->handle($request, $data, true);

        if ($nodeTypeFormHandler->isRequestValid()) {
            $layoutType = $this->formDataToModelTransformer->produceLayoutType($data);
            $nodeType = $this->formDataToModelTransformer->produceContentType($data, $contentType, $layoutType);

            try {
                $this->contentTypeRepository->update($nodeType);
            } catch (\Exception $e) {

            }

            $this->setFlash('success', $this->trans('nodeTypeUpdatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        return $this->view('@backend/content_builder/content_type/edit.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'routingStrategies' => $this->getRoutingStrategies($contentType),
            'model' => $data,
            'errors' => $nodeTypeFormHandler->getErrors(),
            'cleaningResult' => $nodeTypeFormHandler->getCleaningResult(),
        ]);
    }

    private function getFieldTypes(): array
    {
        $types = [];

        foreach ($this->fieldTypeMappingRegistry->all() as $type => $data) {
            $types[$type] = [
                'id' => $type,
                'label' => $data['label'],
                'configuration' => $data['configuration'],
                'constraints' => $data['constraints'],
            ];
        }

        return $types;
    }

    private function getRoutingStrategies(string $contentType): array
    {
        $strategies = [];

        foreach ($this->strategyRegistry->all() as $strategy) {
            if ($strategy->supports($contentType) === false) {
                continue;
            }

            $strategies[] = [
                'id' => $strategy->getId(),
                'label' => $this->trans(sprintf('contentTypeStrategy_%s', $strategy->getId()), [], 'content_builder'),
            ];
        }

        return $strategies;
    }
}
