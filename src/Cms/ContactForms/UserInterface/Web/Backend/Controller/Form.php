<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContactForms\Application\FieldType\Parser\RegistryInterface as FieldParserInterface;
use Tulia\Cms\ContactForms\Application\FieldType\RegistryInterface;
use Tulia\Cms\ContactForms\Domain\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForms\Domain\WriteModel\FormRepository;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\ContactForms\UserInterface\Web\Backend\Form\Form as FormType;
use Tulia\Cms\ContactForms\UserInterface\Web\Backend\Form\FormManagerFactory;
use Tulia\Cms\ContactForms\UserInterface\Web\Backend\Form\ModelTransformer\DomainModelTransformer;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractController
{
    private FormRepository $repository;

    private RegistryInterface $typesRegistry;

    private FieldParserInterface $parsersRegistry;

    public function __construct(
        FormRepository $repository,
        RegistryInterface $typesRegistry,
        FieldParserInterface $parsersRegistry
    ) {
        $this->repository = $repository;
        $this->typesRegistry = $typesRegistry;
        $this->parsersRegistry = $parsersRegistry;
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.form.list');
    }

    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder): ViewInterface
    {
        return $this->view('@backend/forms/index.tpl', [
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @CsrfToken(id="form")
     */
    public function create(Request $request, DomainModelTransformer $transformer)
    {
        $model = $this->repository->createNew();

        $form = $this->createForm(FormType::class, $transformer->transform($model));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $transformer->reverseTransform($form->getData(), $model);

                $this->repository->insert($model);

                $this->setFlash('success', $this->trans('formSaved', [], 'forms'));
                return $this->redirectToRoute('backend.form.edit', [ 'id' => $model->getId() ]);
            } catch (InvalidFieldNameException $e) {
                $error = new FormError($this->trans('formFieldNameContainsInvalidName', ['name' => $e->getName()], 'forms'));
                $form->get('fields_template')->addError($error);
            }
        }

        if ($form->isSubmitted()) {
            $errors = $this->getErrorMessages($form);
        }

        $availableFields = [];

        foreach ($this->parsersRegistry->all() as $field) {
            $definition = $field->getDefinition();
            $name = $field->getName();

            $availableFields[$name] = [
                'name' => $name,
                'label' => $definition['name'],
                'options' => $definition['options'],
            ];
        }

        $fields = [];

        foreach ($request->request->get('form')['fields'] ?? [] as $key => $options) {
            $type = $options['type'];
            unset($options['type']);

            foreach ($options as $name => $value) {
                $options[$name] = [
                    'name' => $name,
                    'value' => $value,
                    'error' => $errors['fields'][$key][$name][0] ?? null
                ];
            }

            $fields[] = [
                'type' => $type,
                'options' => $options,
            ];
        }

        return $this->view('@backend/forms/create.tpl', [
            'model' => $model,
            'form' => $form->createView(),
            'fieldTypes' => $this->typesRegistry->all(),
            'fields' => $fields,
            'availableFields' => $availableFields,
        ]);
    }

    private function getErrorMessages($form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
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

    /**
     * @CsrfToken(id="form")
     */
    public function edit(
        Request $request,
        FormManagerFactory $factory,
        string $id
    ) {
        $model = $this->getFormById($id);
        $manager = $factory->create($model);
        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $manager->save($form);

                $this->setFlash('success', $this->trans('formSaved', [], 'forms'));
                return $this->redirectToRoute('backend.form.edit', [ 'id' => $model->getId() ]);
            } catch (InvalidFieldNameException $e) {
                $error = new FormError($this->trans('formFieldNameContainsInvalidName', ['name' => $e->getName()], 'forms'));
                $form->get('fields_template')->addError($error);
            }
        }

        return $this->view('@backend/forms/edit.tpl', [
            'model' => $model,
            'form'  => $form->createView(),
            'fieldParsers' => $this->parsersRegistry->all(),
        ]);
    }
}
