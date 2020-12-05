<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authentication\LoginCredentials;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractLoginCredentials implements LoginCredentialsInterface
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * {@inheritdoc}
     */
    public function get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, string $value): void
    {
        $this->data[$name] = $value;
    }
}
