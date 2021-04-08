<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig\Extension;

use Tulia\Component\Routing\Enum\SslModeEnum;
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
    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(CurrentWebsiteInterface $currentWebsite, RegistryInterface $registry)
    {
        $this->currentWebsite = $currentWebsite;
        $this->registry       = $registry;
    }

    /**
     * {@inheritdoc}
     */
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
