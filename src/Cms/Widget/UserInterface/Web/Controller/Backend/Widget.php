<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Widget\Application\Command\WidgetStorage;
use Tulia\Cms\Widget\Application\Exception\TranslatableWidgetException;
use Tulia\Cms\Widget\Application\Model\Widget as ApplicationWidget;
use Tulia\Cms\Widget\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\Widget\Query\Enum\ScopeEnum;
use Tulia\Cms\Widget\Query\Exception\MultipleFetchException;
use Tulia\Cms\Widget\Query\Exception\QueryException;
use Tulia\Cms\Widget\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Widget\Query\Factory\WidgetFactoryInterface;
use Tulia\Cms\Widget\Query\FinderFactoryInterface;
use Tulia\Cms\Widget\Query\Model\Widget as QueryModelWidget;
use Tulia\Cms\Widget\UserInterface\Web\Form\WidgetForm;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Widget\Exception\WidgetNotFoundException;
use Tulia\Component\Widget\Registry\WidgetRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Widget extends AbstractController
{
    protected FinderFactoryInterface $finderFactory;
    protected WidgetRegistryInterface $widgetRegistry;
    protected WidgetStorage $widgetStorage;

    public function __construct(
        FinderFactoryInterface $finderFactory,
        WidgetRegistryInterface $widgetRegistry,
        WidgetStorage $widgetStorage
    ) {
        $this->finderFactory  = $finderFactory;
        $this->widgetRegistry = $widgetRegistry;
        $this->widgetStorage  = $widgetStorage;
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
     * @param Request $request
     * @param string $id
     * @param WidgetFactoryInterface $widgetFactory
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws NotFoundHttpException
     * @throws WidgetNotFoundException
     *
     * @CsrfToken(id="widget_form")
     */
    public function create(Request $request, string $id, WidgetFactoryInterface $widgetFactory)
    {
        if ($this->widgetRegistry->has($id) === false) {
            throw $this->createNotFoundException($this->trans('widgetNotFound', [], 'widgets'));
        }

        $widgetModel = $widgetFactory->createNew([
            'widget_id' => $id,
            'space'     => $request->query->get('space', ''),
        ]);

        $widgetInstance = $this->widgetRegistry->get($id);

        $model = ApplicationWidget::fromQueryModel($widgetModel);

        $widgetConfiguration = $model->getWidgetConfiguration();
        $configs = $widgetConfiguration->all();
        $widgetInstance->configure($widgetConfiguration);
        $widgetConfiguration->merge($configs);

        $model->setWidgetConfiguration($widgetConfiguration);

        $form = $this->createForm(WidgetForm::class, $model, [
            'widget_form' => $widgetInstance->getForm($widgetConfiguration),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $widgetInstance->saveAction($form, $data->getWidgetConfiguration());
            $this->widgetStorage->save($data);

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
     * @param Request $request
     * @param string $id
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws NotFoundHttpException
     * @throws WidgetNotFoundException
     *
     * @CsrfToken(id="widget_form")
     */
    public function edit(
        Request $request,
        string $id
    ) {
        $widgetModel = $this->getWidgetById($id);

        if ($widgetModel === false) {
            throw $this->createNotFoundException($this->trans('widgetNotFound', [], 'widgets'));
        }

        $widgetInstance = $this->widgetRegistry->get($widgetModel->getWidgetId());

        $model = ApplicationWidget::fromQueryModel($widgetModel);

        $widgetConfiguration = $model->getWidgetConfiguration();
        $configs = $widgetConfiguration->all();
        $widgetInstance->configure($widgetConfiguration);
        $widgetConfiguration->merge($configs);

        $model->setWidgetConfiguration($widgetConfiguration);

        $form = $this->createForm(WidgetForm::class, $model, [
            'widget_form' => $widgetInstance->getForm($widgetConfiguration),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $widgetInstance->saveAction($form, $data->getWidgetConfiguration());
            $this->widgetStorage->save($data);

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
            'model'      => $widgetModel,
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
                $widget = $this->getWidgetById($id);
            } catch (NotFoundHttpException $e) {
                continue;
            }

            try {
                $this->widgetStorage->delete(ApplicationWidget::fromQueryModel($widget));
                $removedWidgets++;
            } catch (TranslatableWidgetException $e) {
                $this->setFlash('warning', $this->transObject($e));
            }
        }

        if ($removedWidgets) {
            $this->setFlash('success', $this->trans('selectedWidgetsWereDeleted', [], 'widgets'));
        }

        return $this->redirectToRoute('backend.widget');
    }

    /**
     * @param string $id
     *
     * @return QueryModelWidget
     *
     * @throws NotFoundHttpException
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getWidgetById(string $id): QueryModelWidget
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria(['id' => $id]);
        $finder->fetchRaw();

        $widget = $finder->getResult()->first();

        if (! $widget) {
            throw $this->createNotFoundException($this->trans('widgetNotFound', [], 'widgets'));
        }

        return $widget;
    }
}
