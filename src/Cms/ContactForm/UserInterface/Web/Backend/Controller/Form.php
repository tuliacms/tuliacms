<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Backend\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContactForm\Domain\Exception\FormNotFoundException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForm\Domain\FieldsParser\Exception\MultipleFieldsInTemplateException;
use Tulia\Cms\ContactForm\Domain\FieldType\FieldsTypeRegistryInterface;
use Tulia\Cms\ContactForm\Domain\WriteModel\FormRepository;
use Tulia\Cms\ContactForm\Infrastructure\Persistence\Domain\ReadModel\Datatable\DatatableFinder;
use Tulia\Cms\ContactForm\UserInterface\Web\Backend\Form\Form as FormType;
use Tulia\Cms\ContactForm\UserInterface\Web\Backend\Form\ModelTransformer\DomainModelTransformer;
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

    private FieldsTypeRegistryInterface $typesRegistry;

    public function __construct(
        FormRepository $repository,
        FieldsTypeRegistryInterface $typesRegistry
    ) {
        $this->repository = $repository;
        $this->typesRegistry = $typesRegistry;
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.contact_form.list');
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

                $this->setFlash('success', $this->trans('formSaved', [], 'contact-form'));
                return $this->redirectToRoute('backend.contact_form.edit', [ 'id' => $model->getId() ]);
            } catch (InvalidFieldNameException $e) {
                $error = new FormError($this->trans('formFieldNameContainsInvalidName', ['name' => $e->getName()], 'contact-form'));
                $form->get('fields_template')->addError($error);
            }
        }

        return $this->view('@backend/forms/create.tpl', [
            'model' => $model,
            'form' => $form->createView(),
            'fieldTypes' => $this->typesRegistry->all(),
            'fields' => $this->collectFieldsFromRequest($request, $form),
            'availableFields' => $this->collectAvailableFields(),
        ]);
    }

    /**
     * @CsrfToken(id="form")
     */
    public function edit(Request $request, DomainModelTransformer $transformer, string $id)
    {
        $model = $this->repository->find($id);

        $form = $this->createForm(FormType::class, $transformer->transform($model));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $transformer->reverseTransform($form->getData(), $model);

                $this->repository->update($model);

                $this->setFlash('success', $this->trans('formSaved', [], 'contact-form'));
                return $this->redirectToRoute('backend.contact_form.edit', [ 'id' => $model->getId() ]);
            } catch (InvalidFieldNameException $e) {
                $error = new FormError($this->trans('formFieldNameContainsInvalidName', ['name' => $e->getName()], 'contact-form'));
                $form->get('fields_template')->addError($error);
            } catch (MultipleFieldsInTemplateException $e) {
                $error = new FormError($this->trans('multipleFieldOccuredInTemplate', ['name' => $e->getName()], 'contact-form'));
                $form->get('fields_template')->addError($error);
            }
        }

        if ($request->request->has('form')) {
            $fields = $this->collectFieldsFromRequest($request, $form);
        } else {
            $fields = $this->convertFieldsForViewFormat($model);
        }

        return $this->view('@backend/forms/edit.tpl', [
            'model' => $model,
            'form'  => $form->createView(),
            'fieldTypes' => $this->typesRegistry->all(),
            'fields' => $fields,
            'availableFields' => $this->collectAvailableFields(),
        ]);
    }

    /**
     * @CsrfToken(id="form.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $removedForms = 0;

        foreach ($request->request->get('ids') as $id) {
            try {
                $node = $this->repository->find($id);
            } catch (FormNotFoundException $e) {
                continue;
            }

            $this->repository->delete($node);
            $removedForms++;
        }

        if ($removedForms) {
            $this->setFlash('success', $this->trans('selectedFormsWereRemoved', [], 'contact-form'));
        }

        return $this->redirectToRoute('backend.contact_form.list');
    }

    private function getErrorMessages($form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (! $child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }

    private function collectAvailableFields(): array
    {
        $availableFields = [];

        foreach ($this->typesRegistry->all() as $field) {
            $parser = $this->typesRegistry->getParser($field->getAlias());

            $definition = $parser->getDefinition();
            $alias = $parser->getAlias();

            $availableFields[$alias] = [
                'alias' => $alias,
                'label' => $definition['name'],
                'options' => $definition['options'],
            ];
        }
        return $availableFields;
    }

    private function collectFieldsFromRequest(Request $request, $form): array
    {
        if ($form->isSubmitted()) {
            $errors = $this->getErrorMessages($form);
        }

        $fields = [];

        foreach ($request->request->get('form')['fields'] ?? [] as $key => $options) {
            $alias = $options['alias'];
            unset($options['alias']);

            foreach ($options as $name => $value) {
                $options[$name] = [
                    'name' => $name,
                    'value' => $value,
                    'error' => $errors['fields'][$key][$name][0] ?? null,
                ];
            }

            $fields[] = [
                'alias' => $alias,
                'options' => $options,
            ];
        }

        return $fields;
    }

    private function convertFieldsForViewFormat(\Tulia\Cms\ContactForm\Domain\WriteModel\Model\Form $model): array
    {
        $fields = [];

        foreach ($model->fields() as $field) {
            $alias = $field->getTypeAlias();

            foreach ($field->getOptions() as $name => $value) {
                if ($name === 'constraints') {
                    continue;
                }
                // Rename for Vue model.
                if ($name === 'constraints_raw') {
                    $name = 'constraints';
                }

                $options[$name] = [
                    'name' => $name,
                    'value' => $value,
                    'error' => null,
                ];
            }

            $options['name'] = [
                'name' => 'name',
                'value' => $field->getName(),
                'error' => null,
            ];

            $fields[] = [
                'alias' => $alias,
                'options' => $options,
            ];
        }

        return $fields;
    }
}
