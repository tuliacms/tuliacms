<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Website\Domain\ReadModel\Finder\WebsiteFinderScopeEnum;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteRepositoryInterface;
use Tulia\Cms\Website\Infrastructure\Persistence\Domain\ReadModel\Finder\DbalFinder;
use Tulia\Cms\Website\UserInterface\Web\Form\WebsiteForm;
use Tulia\Cms\Website\UserInterface\Web\Service\WebsiteRequestExtractor;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Website extends AbstractController
{
    private DbalFinder $finder;
    private WebsiteRepositoryInterface $repository;
    private WebsiteRequestExtractor $requestExtractor;

    public function __construct(DbalFinder $finder, WebsiteRepositoryInterface $repository, WebsiteRequestExtractor $requestExtractor)
    {
        $this->finder = $finder;
        $this->repository = $repository;
        $this->requestExtractor = $requestExtractor;
    }

    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('backend.website.list');
    }

    /**
     * @return RedirectResponse|ViewInterface
     */
    public function list()
    {
        $result = $this->finder->find([], WebsiteFinderScopeEnum::BACKEND_LISTING);

        return $this->view('@backend/website/list.tpl', [
            'websites' => $result,
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="website_form")
     */
    public function create(Request $request)
    {
        $website = $this->repository->createNew(
            $this->requestExtractor->extractFromRequest($request)
        );
        $form = $this->createForm(WebsiteForm::class, $website);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->create($form->getData());

            $this->setFlash('success', $this->trans('websiteSaved', [], 'websites'));
            return $this->redirectToRoute('backend.website');
        }

        return $this->view('@backend/website/create.tpl', [
            'website' => $website,
            'form'    => $form->createView(),
            'locale_defaults' => [
                'domain' => $request->getHttpHost(),
                'locale' => $request->getPreferredLanguage(),
            ],
        ]);
    }

    /**
     * @param string $id
     * @param Request $request
     * @return RedirectResponse|ViewInterface
     * @throws NotFoundHttpException
     * @CsrfToken(id="website_form")
     */
    public function edit(string $id, Request $request)
    {
        $website = $this->repository->find($id);
        $form = $this->createForm(WebsiteForm::class, $website);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->update($form->getData());

            $this->setFlash('success', $this->trans('websiteSaved', [], 'websites'));
            return $this->redirectToRoute('backend.website');
        }

        return $this->view('@backend/website/edit.tpl', [
            'website' => $website,
            'form'    => $form->createView(),
            'locale_defaults' => [
                'domain' => $request->getHttpHost(),
                'locale' => $request->getPreferredLanguage(),
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @CsrfToken(id="website.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $website = $this->repository->find($request->request->get('id'));

        try {
            $this->repository->delete($website->getId()->getValue());
            $this->setFlash('success', $this->trans('selectedWebsitesWereDeleted', [], 'websites'));
        } catch (TranslatableWebsiteException $e) {
            $this->setFlash('warning', $this->transObject($e));
        }

        return $this->redirectToRoute('backend.website');
    }
}
