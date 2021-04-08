<?php

declare(strict_types=1);

namespace Tulia\Cms\Profiler\UI\Web\Controller;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Framework\Kernel\Profiler\Profiler;

/**
 * @author Adam Banaszkiewicz
 */
class Toolbar extends AbstractController
{
    /**
     * @var Profiler
     */
    private $profiler;

    /**
     * @param Profiler $profiler
     */
    public function __construct(Profiler $profiler)
    {
        $this->profiler = $profiler;
    }

    public function index(string $token): ViewInterface
    {
        $profile = $this->profiler->loadProfile($token);

        if ($profile === null) {
            return $this->view('@backend/profiler/profiler/profile-not-found.tpl');
        }

        $templates = $this->container->getParameter('profiler.templates');

        return $this->view('@backend/profiler/profiler/toolbar.tpl', [
            'templates' => $templates,
            'profile'   => $profile,
        ]);
    }
}
