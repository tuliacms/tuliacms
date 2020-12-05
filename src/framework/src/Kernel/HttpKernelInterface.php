<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel;

use Symfony\Component\HttpFoundation\Response;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\KernelException;

/**
 * @author Adam Banaszkiewicz
 */
interface HttpKernelInterface
{
    /**
     * @param Request $request
     */
    public function bootstrap(Request $request): void;

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws KernelException
     */
    public function handle(Request $request): Response;

    /**
     * @param Request  $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response): void;

    /**
     * @param \Throwable $throwable
     * @param Request    $request
     *
     * @return Response
     */
    public function handleThrowable(\Throwable $throwable, Request $request): Response;
}
