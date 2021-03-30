<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\Matcher\Matcher;
use Tulia\Component\Routing\Website\RegistryInterface;
use Tulia\Framework\Kernel\Event\BootstrapEvent;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteResolver implements EventSubscriberInterface
{
    protected RegistryInterface $websites;
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(RegistryInterface $websites, CurrentWebsiteInterface $currentWebsite)
    {
        $this->websites = $websites;
        $this->currentWebsite = $currentWebsite;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BootstrapEvent::class => [
                ['handle', 9900]
            ],
        ];
    }

    public function handle(BootstrapEvent $event): void
    {
        $request = $event->getRequest();
        $requestScheme = $request->getScheme();
        $requestDomain = $request->getHttpHost();
        $pathinfo      = $request->getPathInfo();

        $currentWebsite = Matcher::matchRequestAgainstObjects($request, $this->websites);

        if ($currentWebsite->getLocale()->getSslMode() !== SslModeEnum::ALLOWED_BOTH) {
            $redirect = null;
            $baseurl  = $requestDomain . $pathinfo;

            if ($requestScheme === 'http' && $currentWebsite->getLocale()->getSslMode() === SslModeEnum::FORCE_SSL) {
                $redirect = new RedirectResponse('https://' . $baseurl, Response::HTTP_MOVED_PERMANENTLY);
            } elseif ($requestScheme === 'https' && $currentWebsite->getLocale()->getSslMode() === SslModeEnum::FORCE_NON_SSL) {
                $redirect = new RedirectResponse('http://' . $baseurl, Response::HTTP_MOVED_PERMANENTLY);
            }

            if ($redirect) {
                //dump($redirect);
                exit;
                //$event->setResponse($redirect);
                //return;
            }
        }

        if ($currentWebsite->getLocale()->getPathPrefix()) {
            $pathinfo = substr($pathinfo, \strlen($currentWebsite->getLocale()->getPathPrefix()));
        }

        $request->attributes->set('_content_path', $pathinfo);
        $request->attributes->set('_website', $currentWebsite);

        $this->currentWebsite->set($currentWebsite);
    }
}
