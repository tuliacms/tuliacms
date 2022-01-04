<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\LayoutTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\LayoutSectionType;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\NodeTypeForm;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\RequestManipulator\NodeTypeRequestManipulator;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\RequestManipulator\NodeTypeValidationRequestManipulator;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Transformer\NodeTypeModelToFormDataTransformer;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Validator\CodenameValidator;
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

    public function __construct(
        NodeTypeRegistry $nodeTypeRegistry,
        LayoutTypeRegistry $layoutTypeRegistry,
        FieldTypeMappingRegistry $fieldTypeMappingRegistry
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->layoutTypeRegistry = $layoutTypeRegistry;
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
    }

    /**
     * @CsrfToken(id="create-node-type")
     */
    public function create(Request $request): ViewInterface
    {
        $errors = [];
        $data = [];
        $cleaningResult = [];

        if ($request->isMethod('POST')) {
            $data = json_decode($request->request->get('node_type'), true);

            $validationDataManipulator = new NodeTypeValidationRequestManipulator();

            $formData = $validationDataManipulator->cleanFromValidationData($data);

            $dataManipulator = new NodeTypeRequestManipulator(
                $formData,
                $this->fieldTypeMappingRegistry,
                new CodenameValidator()
            );
            $formData = $dataManipulator->cleanForSulprusData();
            $cleaningResult = $dataManipulator->getCleaningResult();

            $formsAreValid = true;

            // Node type form
            $request = Request::create('/', 'POST');
            $request->request->set('node_type_form', $formData['type']);
            $form = $this->createForm(NodeTypeForm::class, null, [
                'fields' => $this->collectFieldsFromSections($formData['layout'])
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

            } else {
                $formsAreValid = false;
                $errors['type'] = $this->getErrorMessages($form);
            }


            // Layout sidebar section form
            $request = Request::create('/', 'POST');
            $request->request->set('layout_section', $formData['layout']['sidebar']);
            $form = $this->createForm(LayoutSectionType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

            } else {
                $formsAreValid = false;
                $errors['layout']['sidebar'] = $this->getErrorMessages($form);
            }


            // Layout main section form
            $request = Request::create('/', 'POST');
            $request->request->set('layout_section', $formData['layout']['main']);
            $form = $this->createForm(LayoutSectionType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

            } else {
                $formsAreValid = false;
                $errors['layout']['main'] = $this->getErrorMessages($form);
            }

            if ($formsAreValid) {
                dump('valid!');
                //exit;
            }

            $data = $validationDataManipulator->joinErrorsWithData($formData, $errors);
        }

        return $this->view('@backend/content_builder/node_type/create.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'model' => $data,
            'errors' => $errors,
            'cleaningResult' => $cleaningResult,
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

        $errors = [];
        $data = [];
        $cleaningResult = [];

        $layout = $this->layoutTypeRegistry->get($type->getLayout());
        $data = (new NodeTypeModelToFormDataTransformer())->transform($type, $layout);

        if ($request->isMethod('POST')) {
            $data = json_decode($request->request->get('node_type'), true);

            $validationDataManipulator = new NodeTypeValidationRequestManipulator();

            $formData = $validationDataManipulator->cleanFromValidationData($data);

            $dataManipulator = new NodeTypeRequestManipulator(
                $formData,
                $this->fieldTypeMappingRegistry,
                new CodenameValidator()
            );
            $formData = $dataManipulator->cleanForSulprusData();
            $cleaningResult = $dataManipulator->getCleaningResult();

            $formsAreValid = true;

            // Node type form
            $request = Request::create('/', 'POST');
            $request->request->set('node_type_form', $formData['type']);
            $form = $this->createForm(NodeTypeForm::class, null, [
                'fields' => $this->collectFieldsFromSections($formData['layout']),
                'edit_form' => true,
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

            } else {
                $formsAreValid = false;
                $errors['type'] = $this->getErrorMessages($form);
            }


            // Layout sidebar section form
            $request = Request::create('/', 'POST');
            $request->request->set('layout_section', $formData['layout']['sidebar']);
            $form = $this->createForm(LayoutSectionType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

            } else {
                $formsAreValid = false;
                $errors['layout']['sidebar'] = $this->getErrorMessages($form);
            }


            // Layout main section form
            $request = Request::create('/', 'POST');
            $request->request->set('layout_section', $formData['layout']['main']);
            $form = $this->createForm(LayoutSectionType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

            } else {
                $formsAreValid = false;
                $errors['layout']['main'] = $this->getErrorMessages($form);
            }

            if ($formsAreValid) {
                dump('valid!');
                //exit;
            }

            $data = $validationDataManipulator->joinErrorsWithData($formData, $errors);
        }

        return $this->view('@backend/content_builder/node_type/edit.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'model' => $data,
            'errors' => $errors,
            'cleaningResult' => $cleaningResult,
        ]);
    }

    private function getErrorMessages(FormInterface $form): array {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
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

    private function collectFieldsFromSections(array $layout): array
    {
        $fields = [];

        foreach ($layout as $group) {
            foreach ($group['sections'] as $section) {
                $fields += $section['fields'];
            }
        }

        return $fields;
    }
}
