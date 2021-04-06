<?php

declare(strict_types=1);

namespace Tulia\Framework\Kernel;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Controller\ArgumentResolverInterface;
use Tulia\Framework\Kernel\Event;
use Tulia\Framework\Kernel\Controller\ControllerResolverInterface;

/**
 * @author Adam Banaszkiewicz
 */
class HttpKernel implements HttpKernelInterface
{
    protected EventDispatcherInterface $dispatcher;
    protected ControllerResolverInterface $controllerResolver;
    protected ArgumentResolverInterface $argumentResolver;
    protected RequestStack $requestStack;

    public function __construct(EventDispatcherInterface $dispatcher, ControllerResolverInterface $controllerResolver, ArgumentResolverInterface $argumentResolver, RequestStack $requestStack)
    {
        $this->dispatcher = $dispatcher;
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver = $argumentResolver;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap(Request $request): void
    {
        $event = new Event\BootstrapEvent($this, $request);
        $this->dispatcher->dispatch($event);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): Response
    {
        $this->requestStack->push($request);

        try {
            return $this->handleRaw($request);
        } catch (\Throwable $e) {
            return $this->handleThrowable($e, $request);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleThrowable(\Throwable $throwable, Request $request): Response
    {
        $event = new Event\ExceptionEvent($this, $request, $throwable);
        $this->dispatcher->dispatch($event);

        $e = $event->getThrowable();

        if (!$event->hasResponse()) {
            $this->finishRequest($request);

            throw $e;
        }

        $response = $event->getResponse();

        if (!$response->isClientError() && !$response->isServerError() && !$response->isRedirect()) {
            $response->setStatusCode(500);
        }

        try {
            return $this->filterResponse($response, $request);
        } catch (\Exception $e) {
            return $response;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(Request $request, Response $response): void
    {
        $this->dispatcher->dispatch(new Event\TerminateEvent($this, $request, $response));
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws Exception\ControllerDoesNotReturnResponseException
     * @throws Exception\ControllerNotCallableException
     * @throws Exception\NotFoundHttpException
     */
    private function handleRaw(Request $request): Response
    {
        $event = new Event\RequestEvent($this, $request);
        $this->dispatcher->dispatch($event);

        if ($event->hasResponse()) {
            return $this->filterResponse($event->getResponse(), $request);
        }

        $controller = $this->controllerResolver->getController($request);

        if (! $controller) {
            throw new Exception\NotFoundHttpException('Controller not resolved. Did You properly register Routing?');
        }

        $event = new Event\ControllerEvent($this, $request, $controller);
        $this->dispatcher->dispatch($event);
        $controller = $event->getController();

        if (is_array($controller)) {
            $arguments = $this->argumentResolver->getArguments($request, $controller[0], $controller[1]);
        }

        $event = new Event\ControllerArgumentsEvent($this, $request, $controller, $arguments);
        $this->dispatcher->dispatch($event);

        $controller = $event->getController();
        $arguments  = $event->getArguments();

        $response = $controller(...$arguments);

        if (! $response instanceof Response) {
            $event = new Event\ViewEvent($this, $request, $response);
            $this->dispatcher->dispatch($event);

            if ($event->hasResponse()) {
                $response = $event->getResponse();
            } else {
                $msg = 'The controller must return a "Symfony\Component\HttpFoundation\Response" object.';

                if (null === $response) {
                    $msg .= ' Did you forget to add a return statement somewhere in your controller?';
                }

                throw new Exception\ControllerDoesNotReturnResponseException($msg);
            }
        }

        return $this->filterResponse($response, $request);
    }

    /**
     * @param Response $response
     * @param Request  $request
     *
     * @return Response
     */
    private function filterResponse(Response $response, Request $request): Response
    {
        $event = new Event\ResponseEvent($this, $request, $response);

        $this->dispatcher->dispatch($event);

        $this->finishRequest($request);

        return $event->getResponse();
    }

    /**
     * @param Request $request
     */
    private function finishRequest(Request $request): void
    {
        $this->dispatcher->dispatch(new Event\FinishRequestEvent($this, $request));
    }
}
