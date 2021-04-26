<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UserInterface\Web\Controller\Backend;

use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\ContactForms\Application\FieldsParser\Exception\InvalidFieldNameException;
use Tulia\Cms\ContactForms\Application\FieldType\RegistryInterface;
use Tulia\Cms\ContactForms\Application\FieldType\Parser\RegistryInterface as FieldParserInterface;
use Tulia\Cms\ContactForms\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\ContactForms\Query\Factory\FormFactoryInterface;
use Tulia\Cms\ContactForms\Query\Model\Form as QueryForm;
use Tulia\Cms\ContactForms\Query\Enum\ScopeEnum;
use Tulia\Cms\ContactForms\Query\FinderFactoryInterface;
use Tulia\Cms\ContactForms\UserInterface\Web\Form\FormManagerFactory;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractController
{
    private FinderFactoryInterface $finderFactory;

    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    /**
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.form.list');
    }

    /**
     * @param Request $request
     * @param DatatableFactory $factory
     * @param DatatableFinder $finder
     *
     * @return ViewInterface
     */
    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder): ViewInterface
    {
        return $this->view('@backend/forms/index.tpl', [
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    /**
     * @param Request $request
     * @param DatatableFactory $factory
     * @param DatatableFinder $finder
     *
     * @return JsonResponse
     */
    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @CsrfToken(id="form")
     */
    public function create(
        Request $request,
        FormManagerFactory $factory,
        RegistryInterface $typesRegistry,
        FieldParserInterface $parsersRegistry,
        FormFactoryInterface $formFactory
    ) {
        $model = $formFactory->createNew();
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
                $form->get('fields_source')->addError($error);
            }
        }

        return $this->view('@backend/forms/create.tpl', [
            'model' => $model,
            'form'  => $form->createView(),
            'fieldTypes' => $typesRegistry->all(),
            'fieldParsers' => $parsersRegistry->all(),
        ]);
    }

    /**
     * @CsrfToken(id="form")
     */
    public function edit(
        Request $request,
        FormManagerFactory $factory,
        RegistryInterface $typesRegistry,
        FieldParserInterface $parsersRegistry,
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
            'fieldParsers' => $parsersRegistry->all(),
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
