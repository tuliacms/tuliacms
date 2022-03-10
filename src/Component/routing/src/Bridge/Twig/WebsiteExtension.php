<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Bridge\Twig;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\RegistryInterface;
use Tulia\Component\Routing\Website\WebsiteInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteExtension extends AbstractExtension
{
    protected CurrentWebsiteInterface $currentWebsite;
    protected RegistryInterface $registry;

    public function __construct(CurrentWebsiteInterface $currentWebsite, RegistryInterface $registry)
    {
        $this->currentWebsite = $currentWebsite;
        $this->registry = $registry;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales', function () {
                return $this->currentWebsite->getLocales();
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('locale', function () {
                return $this->currentWebsite->getLocale();
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('current_website', function () {
                return $this->currentWebsite;
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('website_list', function () {
                return $this->registry;
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('website_url', function (WebsiteInterface $website, string $path = null) {
                return $website->getAddress() . $path;
            }, [
                 'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('website_backend_url', function (WebsiteInterface $website, string $path = null) {
                return $website->getBackendAddress() . $path;
            }, [
                 'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
