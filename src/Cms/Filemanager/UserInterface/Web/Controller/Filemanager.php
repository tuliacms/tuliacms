<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Tulia\Cms\Filemanager\CommandPropagatorInterface;
use Tulia\Cms\Filemanager\Exception\CommandNotFoundException;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Component\Security\Http\Csrf\Annotation\IgnoreCsrfToken;

/**
 * @IgnoreCsrfToken
 * @author Adam Banaszkiewicz
 */
class Filemanager extends AbstractController
{
    /**
     * @var CommandPropagatorInterface
     */
    protected $commandPropagator;

    /**
     * @param CommandPropagatorInterface $commandPropagator
     */
    public function __construct(CommandPropagatorInterface $commandPropagator)
    {
        $this->commandPropagator = $commandPropagator;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws CommandNotFoundException
     */
    public function endpoint(Request $request): JsonResponse
    {
        return $this->responseJson($this->commandPropagator->handle((string) $request->query->get('cmd', ''), $request));
    }
}
