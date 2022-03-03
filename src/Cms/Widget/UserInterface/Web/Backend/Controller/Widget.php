<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Form\ContentTypeFormDescriptor;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\ContentFormService;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\IgnoreCsrfToken;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Exception\RequestCsrfTokenException;
use Tulia\Cms\Widget\Domain\Catalog\Registry\WidgetRegistryInterface;
use Tulia\Cms\Widget\Domain\WriteModel\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget as WidgetModel;
use Tulia\Cms\Widget\Domain\WriteModel\WidgetRepository;
use Tulia\Cms\Widget\Infrastructure\Persistence\Domain\ReadModel\Datatable\DatatableFinder;
use Tulia\Component\Datatable\DatatableFactory;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Widget extends AbstractController
{
    private WidgetRegistryInterface $widgetRegistry;
    private WidgetRepository $repository;
    private ContentFormService $contentFormService;
    private ContentTypeRegistryInterface $typeRegistry;

    public function __construct(
        WidgetRegistryInterface $widgetRegistry,
        WidgetRepository $repository,
        ContentFormService $contentFormService,
        ContentTypeRegistryInterface $typeRegistry
    ) {
        $this->widgetRegistry = $widgetRegistry;
        $this->repository = $repository;
        $this->contentFormService = $contentFormService;
        $this->typeRegistry = $typeRegistry;
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
     * @IgnoreCsrfToken()
     * @throws RequestCsrfTokenException
     */
    public function create(Request $request, string $type)
    {
        $this->validateCsrfToken($request, $type);

        $widgetInfo = $this->widgetRegistry->get($type);
        $model = $this->repository->createNew($type);

        $formDescriptor = $this->produceFormDescriptor($model);
        $formDescriptor->handleRequest($request);
        $widgetType = $formDescriptor->getContentType();

        if ($formDescriptor->isFormValid()) {
            $this->updateModel($formDescriptor, $model, 'create');

            $this->setFlash('success', $this->trans('widgetSaved', [], 'widgets'));
            return $this->redirectToRoute('backend.widget');
        }

        return $this->view('@backend/widget/create.tpl', [
            'widgetType' => $widgetType,
            'widget'     => $model,
            'widgetInfo'     => $widgetInfo,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @IgnoreCsrfToken()
     * @throws RequestCsrfTokenException
     */
    public function edit(Request $request, string $id)
    {
        $model = $this->repository->find($id);

        $this->validateCsrfToken($request, $model->getWidgetType());

        $widgetInfo = $this->widgetRegistry->get($model->getWidgetType());

        $formDescriptor = $this->produceFormDescriptor($model);
        $formDescriptor->handleRequest($request);
        $widgetType = $formDescriptor->getContentType();

        if ($formDescriptor->isFormValid()) {
            $this->updateModel($formDescriptor, $model, 'update');

            $this->setFlash('success', $this->trans('widgetSaved', [], 'widgets'));
            return $this->redirectToRoute('backend.widget');
        }

        return $this->view('@backend/widget/edit.tpl', [
            'widgetType' => $widgetType,
            'widget'     => $model,
            'widgetInfo'     => $widgetInfo,
            'formDescriptor' => $formDescriptor,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
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

    private function produceFormDescriptor(WidgetModel $widget): ContentTypeFormDescriptor
    {
        return $this->contentFormService->buildFormDescriptor(
            str_replace('.', '_', 'widget_'.$widget->getWidgetType()),
            $widget->toArray()
        );
    }

    /**
     * @throws RequestCsrfTokenException
     */
    private function validateCsrfToken(Request $request, string $type): void
    {
        /**
         * We must detect token validness manually, cause form name changes for every content type.
         */
        if ($request->isMethod('POST')) {
            $tokenId = 'content_builder_form_widget_' . str_replace('.', '_', $type);
            $csrfToken = $request->request->all()[$tokenId]['_token'] ?? '';

            if ($this->isCsrfTokenValid($tokenId, $csrfToken) === false) {
                throw new RequestCsrfTokenException('CSRF token is invalid. Operation stopped.');
            }
        }
    }

    private function updateModel(ContentTypeFormDescriptor $formDescriptor, WidgetModel $widget, string $strategy): void
    {
        $attributes = $formDescriptor->getData();

        $getValue = function (string $code) use ($attributes) {
            foreach ($attributes as $attribute) {
                if ($attribute->getCode() === $code) {
                    return $attribute->getValue();
                }
            }

            return '';
        };

        $widget->setName($getValue('name'));
        $widget->setSpace($getValue('space'));
        $widget->setName($getValue('name'));
        $widget->setHtmlClass($getValue('html_class'));
        $widget->setHtmlId($getValue('html_id'));
        $widget->setStyles($getValue('styles'));
        $widget->setTitle($getValue('title'));
        $widget->setVisibility((bool) $getValue('visibility'));
        $widget->setAttributes($attributes);

        if ($strategy === 'create') {
            $this->repository->insert($widget);
        } else {
            $this->repository->update($widget);
        }
    }
}
