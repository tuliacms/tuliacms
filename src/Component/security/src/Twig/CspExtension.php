<?php

declare(strict_types=1);

namespace Tulia\Component\Security\Twig;

use Tulia\Component\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

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
