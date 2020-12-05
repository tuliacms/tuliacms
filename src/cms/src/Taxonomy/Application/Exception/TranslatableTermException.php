<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\Exception;

use Tulia\Framework\Translation\TranslatableInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TranslatableTermException extends TermException implements TranslatableInterface
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }
}
