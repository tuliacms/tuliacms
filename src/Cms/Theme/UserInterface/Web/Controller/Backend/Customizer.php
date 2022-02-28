<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Cms\Theme\Infrastructure\Framework\Theme\Customizer\Changeset\Changeset;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Customizer\CustomizerInterface;
use Tulia\Component\Theme\Enum\ChangesetTypeEnum;
use Tulia\Component\Theme\Exception\ChangesetNotFoundException;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Customizer extends AbstractController
{
    protected ManagerInterface $themeManager;
    protected CustomizerInterface $customizer;
    protected StorageInterface $customizerChangesetStorage;

    public function __construct(
        ManagerInterface $manager,
        CustomizerInterface $customizer,
        StorageInterface $storage
    ) {
        $this->themeManager = $manager;
        $this->customizer = $customizer;
        $this->customizerChangesetStorage = $storage;
    }

    public function customizeRedirect(Request $request): RedirectResponse
    {
        $theme = $this->themeManager->getTheme();

        if (! $theme) {
            return $this->redirectToRoute('backend.theme');
        }

        $changeset = $this->customizerChangesetStorage->getTemporaryCopyOfActiveChangeset($theme->getName());

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

        return $this->redirectToRoute('backend.theme.customize', $parameters);
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @throws ChangesetNotFoundException
     */
    public function customize(
        Request $request,
        BuilderInterface $builder,
        CurrentWebsiteInterface $currentWebsite,
        string $theme,
        string $changeset = null
    ) {
        $storage = $this->themeManager->getStorage();

        if ($storage->has($theme) === false) {
            return $this->redirectToRoute('backend.theme');
        }

        if (! $changeset) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $themeObject = $storage->get($theme);

        $changesetItem = $this->customizerChangesetStorage->has($changeset)
            ? $this->customizerChangesetStorage->get($changeset)
            : new Changeset($changeset);

        if ($changesetItem->getType() !== ChangesetTypeEnum::TEMPORARY) {
            return $this->redirectToRoute('backend.theme.customize.current');
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

        $customizerView = $builder->build($changesetItem, $themeObject);

        return $this->view('@backend/theme/customizer/customize.tpl', [
            'theme'      => $themeObject,
            'customizer' => $this->customizer,
            'changeset'  => $changesetItem,
            'customizerView' => $customizerView,
            'previewUrl' => $previewUrl,
            'returnUrl'  => $request->query->get('returnUrl'),
        ]);
    }

    /**
     * @param Request $request
     * @param $theme
     * @param $changeset
     * @return JsonResponse|RedirectResponse
     * @throws ChangesetNotFoundException
     * @CsrfToken(id="theme.customizer.save")
     */
    public function save(Request $request, $theme, $changeset)
    {
        $storage = $this->themeManager->getStorage();

        if ($storage->has($theme) === false) {
            return $this->redirectToRoute('backend.theme');
        }

        $themeObject = $storage->get($theme);

        $changeset = $this->customizerChangesetStorage->has($changeset)
            ? $this->customizerChangesetStorage->get($changeset)
            : new Changeset($changeset);

        if ($changeset->getType() !== ChangesetTypeEnum::TEMPORARY) {
            return $this->responseJson([
                'status' => 'error',
            ]);
        }

        $changeset->setTheme($theme);
        $changeset->setType(ChangesetTypeEnum::TEMPORARY);

        if ($changeset->isEmpty()) {
            $themeChangeset = $this->customizerChangesetStorage->getActiveChangeset($themeObject->getName());

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
        //$changeset->setAuthorId($this->getUser()->getId());

        if ($request->request->get('mode') === 'temporary') {
            $this->customizerChangesetStorage->save($changeset);
        }

        if ($request->request->get('mode') === 'theme') {
            $changeset->setType(ChangesetTypeEnum::ACTIVE);
            $this->customizerChangesetStorage->save($changeset);
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
        if ($this->customizerChangesetStorage->has($changeset)) {
            $changesetItem = $this->customizerChangesetStorage->get($changeset);

            if ($changesetItem->getType() !== ChangesetTypeEnum::TEMPORARY) {
                return $this->redirectToRoute('backend.theme');
            }

            $this->customizerChangesetStorage->remove($changesetItem);
        }

        if (empty($request->query->get('returnUrl')) === false) {
            return $this->redirectToUrl($request->getUriForPath($request->query->get('returnUrl')));
        }

        return $this->redirectToRoute('backend.theme');
    }

    /**
     * @param string $theme
     * @return RedirectResponse
     * @CsrfToken(id="theme.customizer.copy_changeset_from_parent")
     */
    public function copyChangesetFromParent(string $theme): RedirectResponse
    {
        $theme = $this->themeManager->getStorage()->get($theme);

        if (! $theme) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        if (! $theme->getParent()) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $parent  = $this->themeManager->getStorage()->get($theme->getParent());

        $changeset = $this->customizerChangesetStorage->getActiveChangeset($parent->getName());

        if (!$changeset) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $this->customizer->configureFieldsTypes($changeset, $theme);

        $themeChangeset = $this->customizerChangesetStorage->getActiveChangeset($parent->getName());

        if ($themeChangeset && $changeset->getId() !== $themeChangeset->getId()) {
            $changeset->merge($themeChangeset);
        }

        $changeset->setTheme($theme->getName());
        $this->customizerChangesetStorage->save($changeset);

        return $this->redirectToRoute('backend.theme.customize.current');
    }

    /**
     * @param string $theme
     * @return RedirectResponse
     * @CsrfToken(id="theme.customizer.reset")
     */
    public function reset(string $theme): RedirectResponse
    {
        $theme = $this->themeManager->getStorage()->get($theme);

        if (! $theme) {
            return $this->redirectToRoute('backend.theme.customize.current');
        }

        $changeset = $this->customizerChangesetStorage->getActiveChangeset($theme->getName());

        if ($changeset) {
            $this->customizerChangesetStorage->remove($changeset);
        }

        return $this->redirectToRoute('backend.theme.customize.current');
    }
}
