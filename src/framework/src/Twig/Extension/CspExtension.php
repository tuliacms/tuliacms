<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig\Extension;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Tulia\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CspExtension extends AbstractExtension
{
    /**
     * @var ContentSecurityPolicyInterface
     */
    protected $csp;

    /**
     * @param ContentSecurityPolicyInterface $csp
     */
    public function __construct(ContentSecurityPolicyInterface $csp)
    {
        $this->csp = $csp;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('csp_nonce', function (string $type = 'script') {
                $nonce = $this->csp->createNonce();

                $this->csp->addNonce(($type ?? 'script').'-src', $nonce);

                return $nonce;
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
