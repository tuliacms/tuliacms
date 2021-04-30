<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Website\Application\Command\WebsiteStorage;
use Tulia\Cms\Website\Application\Exception\TranslatableWebsiteException;
use Tulia\Cms\Website\Application\Model\Website as ApplicationWebsite;
use Tulia\Cms\Website\Domain\ReadModel\Finder\Enum\ScopeEnum;
use Tulia\Cms\Website\Domain\ReadModel\Finder\Factory\WebsiteFactoryInterface;
use Tulia\Cms\Website\Domain\ReadModel\Finder\Finder;
use Tulia\Cms\Website\Domain\ReadModel\Finder\Model\Website as QueryModelWebsite;
use Tulia\Cms\Website\UserInterface\Web\Form\WebsiteFormManagerFactory;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Website extends AbstractController
{
    private Finder $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
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
        $result = $this->finder->find([], ScopeEnum::BACKEND_LISTING);

        return $this->view('@backend/website/list.tpl', [
            'websites' => $result,
        ]);
    }

    /**
     * @param Request $request
     * @param WebsiteFactoryInterface $websiteFactory
     * @param WebsiteFormManagerFactory $formFactory
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="website_form")
     */
    public function create(
        Request $request,
        WebsiteFactoryInterface $websiteFactory,
        WebsiteFormManagerFactory $formFactory
    ) {
        $website = $websiteFactory->createNewFromRequest($request);
        $manager = $formFactory->create($website);
        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

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
     * @param WebsiteFormManagerFactory $formFactory
     * @return RedirectResponse|ViewInterface
     * @throws NotFoundHttpException
     * @CsrfToken(id="website_form")
     */
    public function edit(
        string $id,
        Request $request,
        WebsiteFormManagerFactory $formFactory
    ) {
        $website = $this->getWebsiteById($id);
        $manager = $formFactory->create($website);
        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->update($form);

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
     * @param WebsiteStorage $storage
     * @return RedirectResponse
     * @throws NotFoundHttpException
     * @CsrfToken(id="website.delete")
     */
    public function delete(Request $request, WebsiteStorage $storage): RedirectResponse
    {
        $website = $this->getWebsiteById($request->request->get('id'));

        try {
            $storage->delete(ApplicationWebsite::fromQueryModel($website));
            $this->setFlash('success', $this->trans('selectedWebsitesWereDeleted', [], 'websites'));
        } catch (TranslatableWebsiteException $e) {
            $this->setFlash('warning', $this->transObject($e));
        }

        return $this->redirectToRoute('backend.website');
    }

    /**
     * @param string $id
     * @return QueryModelWebsite
     * @throws NotFoundHttpException
     */
    private function getWebsiteById(string $id): QueryModelWebsite
    {
        /** @var QueryModelWebsite $website */
        $website = $this->finder->findOne(['id' => $id, 'active' => null], ScopeEnum::BACKEND_SINGLE);

        if (! $website) {
            throw $this->createNotFoundException($this->trans('websiteNotFound', [], 'websites'));
        }

        return $website;
    }
}
