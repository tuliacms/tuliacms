<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig\Extension;

use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class CsrfExtension extends AbstractExtension
{
    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_token', function (string $id) {
                return $this->csrfTokenManager->getToken($id)->getValue();
            }),
            new TwigFunction('csrf_field', function (string $id) {
                return '<input type="hidden" novalidate="novalidate" name="_token" value="' . $this->csrfTokenManager->getToken($id)->getValue() . '" />';
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
