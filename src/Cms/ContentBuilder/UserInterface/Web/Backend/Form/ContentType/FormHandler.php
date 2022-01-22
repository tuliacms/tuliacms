<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType\FormType\LayoutSectionType;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType\FormType\ContentTypeForm;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Validator\CodenameValidator;

/**
 * @author Adam Banaszkiewicz
 */
class FormHandler
{
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;
    private FormFactoryInterface $formFactory;
    private array $cleaningResult = [];
    private array $errors = [];
    private bool $isRequestValid = false;

    public function __construct(
        FieldTypeMappingRegistry $fieldTypeMappingRegistry,
        FormFactoryInterface $formFactory
    ) {
        $this->fieldTypeMappingRegistry = $fieldTypeMappingRegistry;
        $this->formFactory = $formFactory;
    }

    public function isRequestValid(): bool
    {
        return $this->isRequestValid;
    }

    public function handle(Request $request, array $data, bool $editForm = false): array
    {
        $this->isRequestValid = false;

        if ($request->isMethod('POST') === false) {
            return $data;
        }

        $errors = [];
        $data = json_decode($request->request->get('node_type'), true);

        $validationDataManipulator = new ValidationRequestManipulator();
        $formData = $validationDataManipulator->cleanFromValidationData($data);

        $dataManipulator = new RequestDataValidator(
            $formData,
            $this->fieldTypeMappingRegistry,
            new CodenameValidator()
        );
        $formData = $dataManipulator->cleanForInvalidElements();
        $this->cleaningResult = $dataManipulator->getCleaningResult();

        $formsAreValid = true;

        // Node type form
        $request = Request::create('/', 'POST');
        $request->request->set('node_type_form', $formData['type']);
        $form = $this->formFactory->create(ContentTypeForm::class, null, [
            'fields' => $this->collectFieldsFromSections($formData['layout']),
            'edit_form' => $editForm,
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
        $form = $this->formFactory->create(LayoutSectionType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        } else {
            $formsAreValid = false;
            $errors['layout']['sidebar'] = $this->getErrorMessages($form);
        }


        // Layout main section form
        $request = Request::create('/', 'POST');
        $request->request->set('layout_section', $formData['layout']['main']);
        $form = $this->formFactory->create(LayoutSectionType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        } else {
            $formsAreValid = false;
            $errors['layout']['main'] = $this->getErrorMessages($form);
        }

        if ($formsAreValid) {
            $this->isRequestValid = true;
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
            if (!$child->isSubmitted() || !$child->isValid()) {
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
