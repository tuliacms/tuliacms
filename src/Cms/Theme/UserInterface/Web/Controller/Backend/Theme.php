<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Platform\Infrastructure\DefaultTheme\DefaultTheme;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Theme\Application\Exception\ThemeNotFoundException;
use Tulia\Cms\Theme\Application\Service\ThemeActivator;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Theme extends AbstractController
{
    protected ManagerInterface $manager;
    protected ThemeActivator $themeActivator;

    public function __construct(ManagerInterface $manager, ThemeActivator $themeActivator)
    {
        $this->manager = $manager;
        $this->themeActivator = $themeActivator;
    }

    public function index(): ViewInterface
    {
        $themes = $this->manager->getThemes();
        $theme  = $this->manager->getTheme();

        if (\in_array($theme, $themes, true) === true) {
            unset($themes[$theme->getName()]);
            $themes = array_merge([ $theme ], $themes);
        }

        return $this->view('@backend/theme/theme/index.tpl', [
            'themes' => $themes,
            'theme'  => $theme,
            'usesDefaultTheme' => $theme instanceof DefaultTheme
        ]);
    }

    /**
     * @param ActivatorInterface $activator
     * @param string $theme
     *
     * @return RedirectResponse
     *
     * @CsrfToken(id="theme.activate")
     */
    public function activate(Request $request): RedirectResponse
    {
        try {
            $this->themeActivator->activateTheme($request->request->get('theme'));
        } catch (ThemeNotFoundException $e) {
            return $this->redirectToRoute('backend.theme');
        }

        $this->setFlash('success', $this->trans('themeActivated', [], 'themes'));
        return $this->redirectToRoute('backend.theme');
    }
}
