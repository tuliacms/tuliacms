<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Http\ContentSecurityPolicy;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Adam Banaszkiewicz
 */
class ContentSecurityPolicy implements ContentSecurityPolicyInterface
{
    /**
     * @var bool
     */
    protected $active = true;

    /**
     * @var array
     */
    protected $policies = [
        'default-src' => [
            "'none'",
        ],
        'script-src' => [
            "'self'",
            'https://apis.google.com',
            'https://platform.twitter.com',
        ],
        'connect-src' => [
            "'self'",
        ],
        'img-src' => [
            "'self'",
            'data:',
            'https://img.youtube.com',
        ],
        'media-src' => [
            "'self'",
        ],
        'style-src' => [
            "'self'",
            "'unsafe-inline'",
            "https://fonts.googleapis.com",
            "http://fonts.googleapis.com",
        ],
        'font-src' => [
            "'self'",
            "https://fonts.gstatic.com",
            "http://fonts.gstatic.com",
        ],
        'frame-src' => [
            "'self'",
            'https://www.youtube.com',
            'http://www.youtube.com',
            'https://www.vimeo.com',
            'http://www.vimeo.com',
            'https://plusone.google.com',
            'https://facebook.com',
            'https://platform.twitter.com',
        ],
        'child-src' => [
            "'self'",
            'https://www.youtube.com',
            'http://www.youtube.com',
            'https://www.vimeo.com',
            'http://www.vimeo.com',
            'https://plusone.google.com',
            'https://facebook.com',
            'https://platform.twitter.com',
        ]
    ];

    /**
     * {@inheritdoc}
     */
    public function add($rule, $value): void
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                $this->policies[$rule][] = $val;
            }
        } else {
            $this->policies[$rule][] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addNonce(string $rule, string $nonce): void
    {
        $this->add($rule, "'nonce-$nonce'");
    }

    /**
     * {@inheritdoc}
     */
    public function has($rule, $value = null): bool
    {
        if ($rule && $value) {
            if (isset($this->policies[$rule]) && \is_array($this->policies[$rule])) {
                return in_array($value, $this->policies[$rule], true);
            }
        } else {
            return isset($this->policies[$rule]);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($rule): array
    {
        return isset($this->policies[$rule]) ? $this->policies[$rule] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function remove($rule, $value = null): void
    {
        if ($rule && $value) {
            if (isset($this->policies[$rule]) && is_array($this->policies[$rule])) {
                foreach ($this->policies[$rule] as $key => $val) {
                    if ($val == $value) {
                        unset($this->policies[$rule][$key]);
                        break;
                    }
                }
            }
        } else {
            unset($this->policies[$rule]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        $result = [];

        foreach ($this->policies as $key => $policies) {
            $result[] = $key . ' ' . implode(' ', $policies);
        }

        return implode('; ', $result);
    }

    /**
     * {@inheritdoc}
     */
    public function appendToResponse(Response $response): void
    {
        if ($this->active === false) {
            return;
        }

        $response->headers->set('Content-Security-Policy', $this->compile());
    }

    /**
     * {@inheritdoc}
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * {@inheritdoc}
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * {@inheritdoc}
     */
    public function createNonce(): string
    {
        $string = true;
        $bytes  = openssl_random_pseudo_bytes(16, $string);

        return base64_encode($bytes);
    }
}
