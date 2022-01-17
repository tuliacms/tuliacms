<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\LayoutType\LayoutTypeRepository;
use Tulia\Cms\ContentBuilder\Domain\NodeType\NodeTypeRepository;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\FormDataToModelTransformer;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\FormHandler;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\ModelToFormDataTransformer;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeType extends AbstractController
{
    private NodeTypeRegistry $nodeTypeRegistry;
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private FormDataToModelTransformer $formDataToModelTransformer;
    private NodeTypeRepository $nodeTypeRepository;
    private LayoutTypeRepository $layoutTypeRepository;

    public function __construct(
        NodeTypeRegistry $nodeTypeRegistry,
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        FormDataToModelTransformer $formDataToModelTransformer,
        NodeTypeRepository $nodeTypeRepository,
        LayoutTypeRepository $layoutTypeRepository
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->formDataToModelTransformer = $formDataToModelTransformer;
        $this->nodeTypeRepository = $nodeTypeRepository;
        $this->layoutTypeRepository = $layoutTypeRepository;
    }

    /**
     * @CsrfToken(id="create-node-type")
     * @return ViewInterface|RedirectResponse
     */
    public function create(Request $request, FormHandler $nodeTypeFormHandler)
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->request->get('node_type'), true);
        } else {
            $data = [];
        }

        $data = $nodeTypeFormHandler->handle($request, $data);

        if ($nodeTypeFormHandler->isRequestValid()) {
            $layoutType = $this->formDataToModelTransformer->produceLayoutType($data);
            $nodeType = $this->formDataToModelTransformer->produceNodeType($data, $layoutType);

            try {
                $this->nodeTypeRepository->insert($nodeType);
            } catch (\Exception $e) {

            }

            try {
                $this->layoutTypeRepository->insert($layoutType);
            } catch (\Exception $e) {

            }

            $this->setFlash('success', $this->trans('nodeTypeCreatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        return $this->view('@backend/content_builder/node_type/create.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'model' => $data,
            'errors' => $nodeTypeFormHandler->getErrors(),
            'cleaningResult' => $nodeTypeFormHandler->getCleaningResult(),
        ]);
    }

    /**
     * @CsrfToken(id="create-node-type")
     * @return ViewInterface|RedirectResponse
     */
    public function edit(string $code, Request $request, FormHandler $nodeTypeFormHandler)
    {
        if ($this->nodeTypeRegistry->has($code) === false) {
            $this->setFlash('danger', $this->trans('nodeTypeNotExists', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $type = $this->nodeTypeRegistry->get($code);

        if ($type->isInternal()) {
            $this->setFlash('danger', $this->trans('cannotEditInternalNodeType', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        $layout = $type->getLayout();

        $data = (new ModelToFormDataTransformer())->transform($type, $layout);
        $data = $nodeTypeFormHandler->handle($request, $data, true);

        if ($nodeTypeFormHandler->isRequestValid()) {
            $layoutType = $this->formDataToModelTransformer->produceLayoutType($data);
            $nodeType = $this->formDataToModelTransformer->produceNodeType($data, $layoutType);

            try {
                $this->nodeTypeRepository->update($nodeType);
            } catch (\Exception $e) {

            }

            try {
                $this->layoutTypeRepository->update($layoutType);
            } catch (\Exception $e) {

            }

            $this->setFlash('success', $this->trans('nodeTypeUpdatedSuccessfully', [], 'content_builder'));
            return $this->redirectToRoute('backend.content_builder.homepage');
        }

        return $this->view('@backend/content_builder/node_type/edit.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
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
}
