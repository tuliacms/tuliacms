<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface KernelInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request): Response;

    /**
     * @return HttpKernelInterface
     */
    public function getHttpKernel(): HttpKernelInterface;

    /**
     * @return PsrContainerInterface
     */
    public function getContainer(): PsrContainerInterface;

    /**
     * @return string
     */
    public function getCacheDir(): string;

    /**
     * @return string
     */
    public function getProjectDir(): string;

    /**
     * @param string $projectDir
     */
    public function setProjectDir(string $projectDir): void;

    /**
     * @return string
     */
    public function getLogDir(): string;

    /**
     * @return string
     */
    public function getExtensionsDir(): string;

    /**
     * @return int
     */
    public function getStartTime(): float;

    /**
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * @return bool
     */
    public function isDebug(): bool;

    /**
     * @param ContainerBuilderInterface $builder
     */
    public function configureContainer(ContainerBuilderInterface $builder): void;
}
