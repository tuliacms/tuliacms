<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class UserExtension extends AbstractExtension
{
    /**
     * @var AuthenticatedUserProviderInterface
     */
    protected $authenticatedUserProvider;

    /**
     * @param AuthenticatedUserProviderInterface $authenticatedUserProvider
     */
    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider  = $authenticatedUserProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('user', function () {
                return $this->authenticatedUserProvider->getUser();
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('user_locale', function () {
                return $this->authenticatedUserProvider->getUser()->getLocale();
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
