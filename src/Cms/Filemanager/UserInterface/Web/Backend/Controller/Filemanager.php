<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Filemanager\Application\Service\FilemanagerCommandHandler;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Security\Http\Csrf\Annotation\IgnoreCsrfToken;

/**
 * @IgnoreCsrfToken
 * @author Adam Banaszkiewicz
 */
class Filemanager extends AbstractController
{
    protected FilemanagerCommandHandler $commandPropagator;

    public function __construct(FilemanagerCommandHandler $commandPropagator)
    {
        $this->commandPropagator = $commandPropagator;
    }

    public function endpoint(Request $request): JsonResponse
    {
        return $this->responseJson($this->commandPropagator->handle((string) $request->query->get('cmd', ''), $request));
    }
}
