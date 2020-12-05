<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UI\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\Changeset\Changeset;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\Customizer\Builder\ThemeBuilderFactoryInterface;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Customizer\CustomizerInterface;
use Tulia\Component\Theme\Enum\ChangesetTypeEnum;
use Tulia\Component\Theme\Exception\ChangesetNotFoundException;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Customizer extends AbstractController
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var CustomizerInterface
     */
    protected $customizer;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var ThemeBuilderFactoryInterface
     */
    protected $themeBuilderFactory;

    /**
     * @param ManagerInterface $manager
     * @param CustomizerInterface $customizer
     * @param StorageInterface $storage
     * @param ThemeBuilderFactoryInterface $themeBuilderFactory
     */
    public function __construct(
        ManagerInterface $manager,
        CustomizerInterface $customizer,
        StorageInterface $storage,
        ThemeBuilderFactoryInterface $themeBuilderFactory
    ) {
        $this->manager = $manager;
        $this->customizer = $customizer;
        $this->storage = $storage;
        $this->themeBuilderFactory = $themeBuilderFactory;
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function customizeRedirect(Request $request): RedirectResponse
    {
        $theme = $this->manager->getTheme();

        if (! $theme) {
            return $this->redirect('backend.theme');
        }

        $changeset = $this->storage->getTemporaryCopyOfActiveChangeset($theme->getName());

        $parameters = [
            'theme'     => $theme->getName(),
            'changeset' => $changeset->getId(),
        ];

        if ($request->query->has('open')) {
            $parameters['open'] = $request->query->get('open');
        }
        if ($request->query->has('returnUrl')) {
            $parameters['returnUrl'] = $request->query->get('returnUrl');
        }

        return $this->redirect('backend.theme.customize', $parameters);
    }

    /**
     * @param Request $request
     * @param ThemeBuilderFactoryInterface $themeBuilderFactory
     * @param string $theme
     * @param string|null $changeset
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws ChangesetNotFoundException
     */
    public function customize(
        Request $request,
        ThemeBuilderFactoryInterface $themeBuilderFactory,
        CurrentWebsiteInterface $currentWebsite,
        string $theme,
        string $changeset = null
    ) {
        $storage = $this->manager->getStorage();

        if ($storage->has($theme) === false) {
            return $this->redirect('backend.theme');
        }

        if (!$changeset) {
            return $this->redirect('backend.theme.customize.current');
        }

        $themeObject = $storage->get($theme);

        $builder = $themeBuilderFactory->build($themeObject);

        $changesetItem = $this->storage->has($changeset)
            ? $this->storage->get($changeset)
            : new Changeset($changeset);

        if ($changesetItem->getType() !== ChangesetTypeEnum::TEMPORARY) {
            return $this->redirect('backend.theme.customize.current');
        }

        $this->customizer->configureFieldsTypes($changesetItem);

        $parameters = [
            'mode'      => 'customizer',
            'changeset' => $changesetItem->getId(),
            '_locale'   => $currentWebsite->getLocale()->getCode(),
        ];

        if ($request->query->has('open')) {
            $parsed = parse_url($request->query->get('open'));
            $parsed['query'] = array_merge($parsed['query'] ?? [], $parameters);

            $previewUrl = $parsed['path'] . '?' . http_build_query($parsed['query']);
        } else {
            $previewUrl = $this->generateUrl('homepage', $parameters);
        }

        return $this->view('@backend/theme/customizer/customize.tpl', [
            'theme'      => $themeObject,
            'customizer' => $this->customizer,
            'changeset'  => $changesetItem,
            'builder'    => $builder,
            'previewUrl' => $previewUrl,
            'returnUrl'  => $request->query->get('returnUrl'),
        ]);
    }

    /**
     * @param Request $request
     * @param $theme
     * @param $changeset
     *
     * @return JsonResponse|RedirectResponse
     *
     * @throws ChangesetNotFoundException
     *
     * @CsrfToken(id="theme.customizer.save")
     */
    public function save(Request $request, $theme, $changeset)
    {
        $this->validateCsrfToken('theme.customizer.save');

        $storage = $this->manager->getStorage();

        if ($storage->has($theme) === false) {
            return $this->redirect('backend.theme');
        }

        $themeObject = $storage->get($theme);

        $changeset = $this->storage->has($changeset)
            ? $this->storage->get($changeset)
            : new Changeset($changeset);

        if ($changeset->getType() !== ChangesetTypeEnum::TEMPORARY) {
            return $this->responseJson([
                'status' => 'error',
            ]);
        }

        $changeset->setTheme($theme);
        $changeset->setType(ChangesetTypeEnum::TEMPORARY);

        if ($changeset->isEmpty()) {
            $themeChangeset = $this->storage->getActiveChangeset($themeObject->getName());

            if ($themeChangeset && $changeset->getId() !== $themeChangeset->getId()) {
                $changeset->merge($themeChangeset);
            }
        }

        $data = $request->request->get('data');

        if (\is_array($data) === false) {
            $data = [];
        }

        $this->customizer->configureFieldsTypes($changeset);

        $changeset->mergeArray($data);
        $changeset->setAuthorId($this->getUser()->getId());

        if ($request->request->get('mode') === 'temporary') {
            $this->storage->save($changeset);
        }

        if ($request->request->get('mode') === 'theme') {
            $changeset->setType(ChangesetTypeEnum::ACTIVE);
            $this->storage->save($changeset);
        }

        return $this->responseJson([
            'status' => 'success',
        ]);
    }

    /**
     * @param string $changeset
     *
     * @return RedirectResponse
     *
     * @throws ChangesetNotFoundException
     */
    public function left(Request $request, string $changeset): RedirectResponse
    {
        if ($this->storage->has($changeset)) {
            $changesetItem = $this->storage->get($changeset);

            if ($changesetItem->getType() !== ChangesetTypeEnum::TEMPORARY) {
                return $this->redirect('backend.theme');
            }

            $this->storage->remove($changesetItem);
        }

        if (empty($request->query->get('returnUrl')) === false) {
            return $this->redirectToUrl($request->getUriForPath($request->query->get('returnUrl')));
        }

        return $this->redirect('backend.theme');
    }

    /**
     * @param string $theme
     *
     * @return RedirectResponse
     */
    public function copyChangesetFromParent(string $theme): RedirectResponse
    {
        $this->validateCsrfToken('theme.customizer.copy_changeset_from_parent');

        $theme = $this->manager->getStorage()->get($theme);

        if (! $theme) {
            return $this->redirect('backend.theme.customize.current');
        }

        if (! $theme->getParent()) {
            return $this->redirect('backend.theme.customize.current');
        }

        $parent  = $this->manager->getStorage()->get($theme->getParent());

        $changeset = $this->storage->getActiveChangeset($parent->getName());

        if (!$changeset) {
            return $this->redirect('backend.theme.customize.current');
        }

        $this->customizer->configureFieldsTypes($changeset, $theme);

        $themeChangeset = $this->storage->getActiveChangeset($parent->getName());

        if ($themeChangeset && $changeset->getId() !== $themeChangeset->getId()) {
            $changeset->merge($themeChangeset);
        }

        $changeset->setTheme($theme->getName());
        $this->storage->save($changeset);

        return $this->redirect('backend.theme.customize.current');
    }

    /**
     * @param string $theme
     *
     * @return RedirectResponse
     */
    public function reset(string $theme): RedirectResponse
    {
        $this->validateCsrfToken('theme.customizer.reset');

        $theme = $this->manager->getStorage()->get($theme);

        if (! $theme) {
            return $this->redirect('backend.theme.customize.current');
        }

        $changeset = $this->storage->getActiveChangeset($theme->getName());

        if ($changeset) {
            $this->storage->remove($changeset);
        }

        return $this->redirect('backend.theme.customize.current');
    }
}
