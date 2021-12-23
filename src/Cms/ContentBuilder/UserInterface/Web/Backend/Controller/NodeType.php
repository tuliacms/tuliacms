<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service\FieldTypeMappingRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\LayoutSectionType;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\NodeTypeForm;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\RequestManipulator\NodeTypeRequestManipulator;
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
    private FieldTypeMappingRegistry $fieldTypeMappingRegistry;

    public function __construct(
        NodeTypeRegistry $nodeTypeRegistry,
        FieldTypeMappingRegistry $fieldTypeMappingRegistry
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
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

            $dataManipulator = new NodeTypeRequestManipulator(
                $data,
                $this->fieldTypeMappingRegistry,
                new CodenameValidator()
            );
            $data = $dataManipulator->cleanForSulprusData();
            $cleaningResult = $dataManipulator->getCleaningResult();

            $formsAreValid = true;

            // Node type form
            $request = Request::create('/', 'POST');
            $request->request->set('node_type_form', $data['type']);
            $form = $this->createForm(NodeTypeForm::class, null, [
                'fields' => $this->collectFieldsFromSections($data['layout'])
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

            } else {
                $formsAreValid = false;
                $errors['type'] = $this->getErrorMessages($form);
            }


            // Layout sidebar section form
            $request = Request::create('/', 'POST');
            $request->request->set('layout_section', $data['layout']['sidebar']);
            $form = $this->createForm(LayoutSectionType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {

            } else {
                $formsAreValid = false;
                $errors['layout']['sidebar'] = $this->getErrorMessages($form);
            }


            // Layout main section form
            $request = Request::create('/', 'POST');
            $request->request->set('layout_section', $data['layout']['main']);
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
        }

        return $this->view('@backend/content_builder/node_type/create.tpl', [
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
