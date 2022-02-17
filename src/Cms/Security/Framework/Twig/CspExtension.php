<?php

declare(strict_types=1);

namespace Tulia\Cms\Security\Framework\Twig;

use Tulia\Cms\Security\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class CspExtension extends AbstractExtension
{
    protected ContentSecurityPolicyInterface $csp;

    public function __construct(ContentSecurityPolicyInterface $csp)
    {
        $this->csp = $csp;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('csp_nonce', function (string $type = 'script') {
                $nonce = $this->csp->createNonce();

                $this->csp->addNonce(($type ?? 'script') . '-src', $nonce);

                return $nonce;
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
