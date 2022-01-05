<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\FormHandler\NodeTypeFormHandler;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Transformer\NodeTypeModelToFormDataTransformer;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeType extends AbstractController
{
    private NodeTypeRegistry $nodeTypeRegistry;
    private LayoutTypeRegistry $layoutTypeRegistry;
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private FormFactoryInterface $formFactory;

    public function __construct(
        NodeTypeRegistry $nodeTypeRegistry,
        LayoutTypeRegistry $layoutTypeRegistry,
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        FormFactoryInterface $formFactory
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->formFactory = $formFactory;
    }

    /**
     * @CsrfToken(id="create-node-type")
     */
    public function create(Request $request): ViewInterface
    {
        if ($request->isMethod('POST')) {
            $data = json_decode($request->request->get('node_type'), true);
        } else {
            $data = [];
        }

        $handler = new NodeTypeFormHandler($request, $this->fieldTypeMappingRegistry, $this->formFactory);
        $data = $handler->handle($data);

        return $this->view('@backend/content_builder/node_type/create.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'model' => $data,
            'errors' => $handler->getErrors(),
            'cleaningResult' => $handler->getCleaningResult(),
        ]);
    }

    /**
     * @CsrfToken(id="create-node-type")
     * @return ViewInterface|RedirectResponse
     */
    public function edit(string $code, Request $request)
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

        $layout = $this->layoutTypeRegistry->get($type->getLayout());

        $data = (new NodeTypeModelToFormDataTransformer())->transform($type, $layout);

        $handler = new NodeTypeFormHandler($request, $this->fieldTypeMappingRegistry, $this->formFactory);
        $data = $handler->handle($data, true);

        return $this->view('@backend/content_builder/node_type/edit.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'model' => $data,
            'errors' => $handler->getErrors(),
            'cleaningResult' => $handler->getCleaningResult(),
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
