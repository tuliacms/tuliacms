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

        /*$data = [
            'layout' => [
                'sidebar' => [
                    'sections' => [
                        0 => [
                            'id' => 'sdfgdfgdfg',
                            'label' => 'Sample section with errors',
                            'fields' => [
                                0 => [
                                    'metadata' => [
                                        'has_errors' => true,
                                    ],
                                    'code' => [
                                        'value' => 'adsfgsdfghdfgh',
                                        'valid' => false,
                                        'message' => 'INVALID ID',
                                    ],
                                    'name' => [
                                        'value' => 'select field',
                                        'valid' => false,
                                        'message' => 'INVALID LABEL',
                                    ],
                                    'type' => [
                                        'value' => 'select',
                                        'valid' => true,
                                        'message' => null,
                                    ],
                                    'multilingual' => [
                                        'value' => 'true',
                                        'valid' => true,
                                        'message' => null,
                                    ],
                                    'constraints' => [
                                        0 => [
                                            'id' => 'required',
                                            'enabled' => true,
                                            'valid' => true,
                                            'message' => null,
                                            'modificators' => [],
                                        ],
                                    ],
                                    'configuration' => [
                                        ['id' => 'placeholder', 'value' => '132434'],
                                        ['id' => 'choices', 'value' => null, 'valid' => false, 'message' => 'This field is required'],
                                    ],
                                ],
                                1 => [
                                    'metadata' => [
                                        'has_errors' => true,
                                    ],
                                    'code' => [
                                        'value' => 'dthtyjtjy',
                                        'valid' => false,
                                        'message' => 'INVALID ID',
                                    ],
                                    'name' => [
                                        'value' => 'text field',
                                        'valid' => false,
                                        'message' => 'INVALID LABEL',
                                    ],
                                    'type' => [
                                        'value' => 'textarea',
                                        'valid' => true,
                                        'message' => null,
                                    ],
                                    'multilingual' => [
                                        'value' => 'true',
                                        'valid' => true,
                                        'message' => null,
                                    ],
                                    'constraints' => [
                                        0 => [
                                            'id' => 'length',
                                            'enabled' => true,
                                            'valid' => true,
                                            'message' => null,
                                            'modificators' => [
                                                ['id' => 'max', 'value' => '255'],
                                                ['id' => 'min', 'value' => null, 'valid' => false, 'message' => 'This field is required'],
                                            ],
                                        ],
                                    ],
                                    'configuration' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];*/

        if ($request->isMethod('POST')) {
            $data = json_decode($request->request->get('node_type'), true);

            if (0) {
                $data['type']['code'] = 'page';
                $data['layout']['sidebar']['sections'][0]['fields'][0]['configuration'][] = [
                    'id' => 'asdasd',
                    'value' => null,
                ];
                $data['layout']['sidebar']['sections'][] = [
                    'code' => '45f34563&^%b',
                    'name' => 'test section',
                    'fields' => [
                        [
                            'id' => 'o8&TH(N876T',
                            'name' => 'test field',
                            'type' => 'not existent type',
                            'multilingual' => true,
                            'configuration' => [],
                        ],
                    ],
                ];
                $data['layout']['sidebar']['sections'][] = [
                    'code' => 'asd',
                    'name' => 'asd',
                    'fields' => [
                        [
                            'code' => 'qwe',
                            'name' => 'qwe',
                            'type' => 'select',
                            'multilingual' => true,
                            'configuration' => [],
                            'constraints' => [
                                [
                                    'id' => 'length',
                                    'modificators' => [
                                        ['id' => 'asdasd', 'value' => '132434'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }

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

        dump($cleaningResult, $errors);

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

        $layout = $this->layoutTypeRegistry->get($type->getLayout());
        $data = (new NodeTypeModelToFormDataTransformer())->transform($type, $layout);

        dump($data);exit;

        return $this->view('@backend/content_builder/node_type/edit.tpl', [
            'fieldTypes' => $this->getFieldTypes(),
            'model' => [],
            'errors' => [],
            'cleaningResult' => [],
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
