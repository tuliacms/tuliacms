<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UI\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Website\Application\Command\WebsiteStorage;
use Tulia\Cms\Website\Application\Exception\TranslatableWebsiteException;
use Tulia\Cms\Website\Application\Model\Website as ApplicationWebsite;
use Tulia\Cms\Website\Query\Enum\ScopeEnum;
use Tulia\Cms\Website\Query\Exception\MultipleFetchException;
use Tulia\Cms\Website\Query\Exception\QueryException;
use Tulia\Cms\Website\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Website\Query\Factory\WebsiteFactoryInterface;
use Tulia\Cms\Website\Query\FinderFactoryInterface;
use Tulia\Cms\Website\Query\Model\Website as QueryModelWebsite;
use Tulia\Cms\Website\UI\Web\Form\WebsiteFormManagerFactory;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\NotFoundHttpException;
use Tulia\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Website extends AbstractController
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    /**
     * @param string|null $list
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    public function list(string $list = null)
    {
        if ($list !== 'list') {
            return $this->redirect('backend.website', ['list' => 'list']);
        }

        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_LISTING);
        $finder->fetchRaw();

        return $this->view('@backend/website/list.tpl', [
            'websites' => $finder->getResult(),
        ]);
    }

    /**
     * @param Request $request
     * @param WebsiteFactoryInterface $websiteFactory
     * @param WebsiteFormManagerFactory $formFactory
     *
     * @return RedirectResponse|ViewInterface
     *
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
            return $this->redirect('backend.website');
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
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     *
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
            $manager->save($form);

            $this->setFlash('success', $this->trans('websiteSaved', [], 'websites'));
            return $this->redirect('backend.website');
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
     *
     * @return RedirectResponse
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     *
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

        return $this->redirect('backend.website');
    }

    /**
     * @param string $id
     *
     * @return QueryModelWebsite
     *
     * @throws NotFoundHttpException
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getWebsiteById(string $id): QueryModelWebsite
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria(['id' => $id]);
        $finder->fetchRaw();

        $website = $finder->getResult()->first();

        if (! $website) {
            throw $this->createNotFoundException($this->trans('websiteNotFound', [], 'websites'));
        }

        return $website;
    }
}
