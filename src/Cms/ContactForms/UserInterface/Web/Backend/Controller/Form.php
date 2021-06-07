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
use Tulia\Cms\ContactForms\Domain\FieldsParser\FieldsParserInterface;
use Tulia\Cms\ContactForms\Domain\WriteModel\FormRepository;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\ContactForms\Query\Enum\ScopeEnum;
use Tulia\Cms\ContactForms\Query\FinderFactoryInterface;
use Tulia\Cms\ContactForms\Query\Model\Form as QueryForm;
use Tulia\Cms\ContactForms\UserInterface\Web\Backend\Form\Form as FormType;
use Tulia\Cms\ContactForms\UserInterface\Web\Backend\Form\FormManagerFactory;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractController
{
    private FinderFactoryInterface $finderFactory;

    private FormRepository $repository;

    private RegistryInterface $typesRegistry;

    private FieldParserInterface $parsersRegistry;

    private FieldsParserInterface $fieldsParser;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        FormRepository $repository,
        RegistryInterface $typesRegistry,
        FieldParserInterface $parsersRegistry,
        FieldsParserInterface $fieldsParser
    ) {
        $this->finderFactory = $finderFactory;
        $this->repository = $repository;
        $this->typesRegistry = $typesRegistry;
        $this->parsersRegistry = $parsersRegistry;
        $this->fieldsParser = $fieldsParser;
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
    public function create(Request $request)
    {
        $model = $this->repository->createNew();

        $form = $this->createForm(FormType::class, $model);
        $form->get('fields_template')->setData('[checkbox name="c" label="Checkbox"]
[consent name="d" label="Zgoda" consent="Wyrażam zgodę"]
[submit name="s" label="Wyślij"]');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $model->setFieldsTemplate(
                    $form->get('fields_template')->getData(),
                    $this->fieldsParser
                );

                $this->repository->insert($model);

                $this->setFlash('success', $this->trans('formSaved', [], 'forms'));
                return $this->redirectToRoute('backend.form.edit', [ 'id' => $model->getId() ]);
            } catch (InvalidFieldNameException $e) {
                $error = new FormError($this->trans('formFieldNameContainsInvalidName', ['name' => $e->getName()], 'forms'));
                $form->get('fields_template')->addError($error);
            }
        }

        return $this->view('@backend/forms/create.tpl', [
            'model' => $model,
            'form' => $form->createView(),
            'fieldTypes' => $this->typesRegistry->all(),
            'fieldParsers' => $this->parsersRegistry->all(),
        ]);
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

    private function getFormById(string $id): QueryForm
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria(['id' => $id, 'fetch_fields' => true]);
        $finder->fetch();

        $form = $finder->getResult()->first();

        if (! $form) {
            throw $this->createNotFoundException($this->trans('formNotFound', [], 'forms'));
        }

        return $form;
    }
}
