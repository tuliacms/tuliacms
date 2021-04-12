<?php

declare(strict_types=1);

namespace Tulia\Cms\Profiler\Infrastructure\Framework\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Tulia\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;
use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Kernel\Profiler\Profiler;
use Tulia\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Toolbar
{
    /**
     * @var Profiler
     */
    private $profiler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ContentSecurityPolicyInterface
     */
    private $csp;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @param Profiler $profiler
     * @param RouterInterface $router
     * @param ContentSecurityPolicyInterface $csp
     * @param EngineInterface $engine
     */
    public function __construct(
        Profiler $profiler,
        RouterInterface $router,
        ContentSecurityPolicyInterface $csp,
        EngineInterface $engine
    ) {
        $this->profiler = $profiler;
        $this->router = $router;
        $this->csp = $csp;
        $this->engine = $engine;
    }

    public function __invoke(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->isAjax()) {
            return;
        }

        $response = $event->getResponse();
        $content  = $response->getContent();

        $toolbar = $this->renderDebugbar($response);

        $content = str_replace('</body>', $toolbar . '</body>', $content);

        $response->setContent($content);
    }

    private function renderDebugbar(Response $response): string
    {
        try {
            $route = $this->router->generate('profiler.toolbar', ['token' => $response->headers->get('X-Debug-Token')]);
        } catch (RouteNotFoundException $e) {
            return '';
        }

        $nonce = $this->csp->createNonce();

        $this->csp->addNonce('script-src', $nonce);

        $path = $this->router->generate('profiler.profile', ['token' => $response->headers->get('X-Debug-Token')]);

        $response->headers->set('X-Debug-Profiler', $this->router->url($path));

        $css = $this->engine->render(new View('@backend/profiler/profiler/toolbar.css.tpl'));
        $js  = $this->engine->render(new View('@backend/profiler/profiler/toolbar.js.tpl'));

        return <<<EOL
<style>{$css}</style>
<div id="profiler-toolbar" class="profiler-toolbar-hidden"></div>
<script nonce="{$nonce}">let __profiler_toolbar_route = '{$route}';{$js}</script>
EOL;
    }
}
