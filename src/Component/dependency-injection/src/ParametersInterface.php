<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

use Tulia\Component\DependencyInjection\Exception\MissingParameterException;

/**
 * @author Adam Banaszkiewicz
 */
interface ParametersInterface
{
    /**
     * @param string $id
     *
     * @return mixed
     *
     * @throws MissingParameterException
     */
    public function getParameter(string $id);

    /**
     * @param string $id
     *
     * @return bool
     */
    public function hasParameter(string $id): bool;

    /**
     * @param string $id
     * @param        $value
     */
    public function setParameter(string $id, $value): void;

    /**
     * @param string $id
     * @param        $value
     */
    public function mergeParameter(string $id, $value): void;

    /**
     * @return array
     */
    public function getParameters(): array;
}
