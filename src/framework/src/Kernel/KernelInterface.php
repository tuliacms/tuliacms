<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;
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

    public function registerPackages(): array;

    public function configureContainer(ContainerBuilder $c, LoaderInterface $loader): void;

    public function registerContainerConfiguration(LoaderInterface $loader): void;
}
