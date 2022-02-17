<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Widget\Domain\WriteModel\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\WriteModel\WidgetRepository;
use Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Datatable\DatatableFinder;
use Tulia\Cms\Widget\UserInterface\Web\Backend\Form\WidgetForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Widget\Registry\WidgetRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Widget extends AbstractController
{
    private WidgetRegistryInterface $widgetRegistry;

    private WidgetRepository $repository;

    public function __construct(
        WidgetRegistryInterface $widgetRegistry,
        WidgetRepository $repository
    ) {
        $this->widgetRegistry = $widgetRegistry;
        $this->repository = $repository;
    }

    /**
     * @return RedirectResponse
     */
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.widget.list');
    }

    public function list(Request $request, DatatableFactory $factory, DatatableFinder $finder): ViewInterface
    {
        return $this->view('@backend/widget/list.tpl', [
            'space' => $request->query->get('space', ''),
            'availableWidgets' => $this->widgetRegistry->all(),
            'datatable' => $factory->create($finder, $request),
        ]);
    }

    public function datatable(Request $request, DatatableFactory $factory, DatatableFinder $finder): JsonResponse
    {
        return $factory->create($finder, $request)->generateResponse();
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="widget_form")
     */
    public function create(Request $request, string $type)
    {
        $model = $this->repository->createNew($type);
        $widgetInstance = $model->getWidgetInstance();
        $widgetConfiguration = $model->getWidgetConfiguration();

        $form = $this->createForm(WidgetForm::class, $model, [
            'widget_form' => $widgetInstance->getForm($widgetConfiguration),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $widgetInstance->saveAction($form, $data->getWidgetConfiguration());
            $this->repository->insert($data);

            $this->setFlash('success', $this->trans('widgetSaved', [], 'widgets'));
            return $this->redirectToRoute('backend.widget');
        }

        $widgetView = $widgetInstance->getView($widgetConfiguration);

        if ($widgetView) {
            $widgetView->addData([
                'config' => $widgetConfiguration,
                'form'   => $form,
            ]);
        }

        return $this->view('@backend/widget/create.tpl', [
            'widgetView' => $widgetView,
            'widget'     => $widgetInstance,
            'form'       => $form->createView(),
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     *
     * @CsrfToken(id="widget_form")
     */
    public function edit(Request $request, string $id)
    {
        $model = $this->repository->find($id);
        $widgetInstance = $model->getWidgetInstance();
        $widgetConfiguration = $model->getWidgetConfiguration();

        $form = $this->createForm(WidgetForm::class, $model, [
            'widget_form' => $widgetInstance->getForm($widgetConfiguration),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $widgetInstance->saveAction($form, $data->getWidgetConfiguration());
            $this->repository->update($data);

            $this->setFlash('success', $this->trans('widgetSaved', [], 'widgets'));
            return $this->redirectToRoute('backend.widget');
        }

        $widgetView = $widgetInstance->getView($widgetConfiguration);

        if ($widgetView) {
            $widgetView->addData([
                'config' => $widgetConfiguration,
                'form'   => $form,
            ]);
        }

        return $this->view('@backend/widget/edit.tpl', [
            'widgetView' => $widgetView,
            'widget'     => $widgetInstance,
            'model'      => $model,
            'form'       => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @CsrfToken(id="widget.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $removedWidgets = 0;

        foreach ($request->request->get('ids') as $id) {
            try {
                $widget = $this->repository->find($id);
            } catch (WidgetNotFoundException $e) {
                continue;
            }

            $this->repository->delete($widget);
            $removedWidgets++;
        }

        if ($removedWidgets) {
            $this->setFlash('success', $this->trans('selectedWidgetsWereDeleted', [], 'widgets'));
        }

        return $this->redirectToRoute('backend.widget');
    }
}
