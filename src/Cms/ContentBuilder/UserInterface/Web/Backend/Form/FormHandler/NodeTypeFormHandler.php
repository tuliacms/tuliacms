<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\FormHandler;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\LayoutSectionType;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\NodeTypeForm;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\RequestManipulator\NodeTypeRequestManipulator;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\RequestManipulator\NodeTypeValidationRequestManipulator;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Validator\CodenameValidator;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeFormHandler
{
    private array $cleaningResult = [];
    private array $errors = [];
    private Request $request;
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private FormFactoryInterface $formFactory;

    public function __construct(
        Request $request,
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        FormFactoryInterface $formFactory
    ) {
        $this->request = $request;
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->formFactory = $formFactory;
    }

    public function handle(array $data, bool $editForm = false): array
    {
        if ($this->request->isMethod('POST') === false) {
            return $data;
        }

        $errors = [];
        $data = json_decode($this->request->request->get('node_type'), true);

        $validationDataManipulator = new NodeTypeValidationRequestManipulator();

        $formData = $validationDataManipulator->cleanFromValidationData($data);

        $dataManipulator = new NodeTypeRequestManipulator(
            $formData,
            $this->fieldTypeMappingRegistry,
            new CodenameValidator()
        );
        $formData = $dataManipulator->cleanForSulprusData();
        $this->cleaningResult = $dataManipulator->getCleaningResult();

        $formsAreValid = true;

        // Node type form
        $this->request = Request::create('/', 'POST');
        $this->request->request->set('node_type_form', $formData['type']);
        $form = $this->formFactory->create(NodeTypeForm::class, null, [
            'fields' => $this->collectFieldsFromSections($formData['layout']),
            'edit_form' => $editForm,
        ]);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

        } else {
            $formsAreValid = false;
            $errors['type'] = $this->getErrorMessages($form);
        }


        // Layout sidebar section form
        $this->request = Request::create('/', 'POST');
        $this->request->request->set('layout_section', $formData['layout']['sidebar']);
        $form = $this->formFactory->create(LayoutSectionType::class);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

        } else {
            $formsAreValid = false;
            $errors['layout']['sidebar'] = $this->getErrorMessages($form);
        }


        // Layout main section form
        $this->request = Request::create('/', 'POST');
        $this->request->request->set('layout_section', $formData['layout']['main']);
        $form = $this->formFactory->create(LayoutSectionType::class);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {

        } else {
            $formsAreValid = false;
            $errors['layout']['main'] = $this->getErrorMessages($form);
        }

        if ($formsAreValid) {
            dump('valid!');
            //exit;
        }

        return $validationDataManipulator->joinErrorsWithData($formData, $errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getCleaningResult(): array
    {
        return $this->cleaningResult;
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
