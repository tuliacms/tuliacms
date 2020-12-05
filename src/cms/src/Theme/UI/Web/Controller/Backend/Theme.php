<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\UI\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Theme\DefaultTheme\DefaultTheme;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Theme\Activator\ActivatorInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Theme extends AbstractController
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return ViewInterface
     */
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
    public function activate(ActivatorInterface $activator, string $theme): RedirectResponse
    {
        $theme = $this->manager->getStorage()->get($theme);

        if (! $theme) {
            return $this->redirect('backend.theme');
        }

        $activator->activate($theme->getName());

        $this->setFlash('success', $this->trans('themeActivated', [], 'themes'));
        return $this->redirect('backend.theme');
    }
}
