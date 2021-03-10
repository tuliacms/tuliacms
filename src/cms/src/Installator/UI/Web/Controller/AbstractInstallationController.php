<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\UI\Web\Controller;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractInstallationController extends AbstractController
{
    protected function stepFinished(Request $request, string $step): bool
    {
        $steps = $request->getSession()->get('installator.steps', []);

        return \is_array($steps) && \in_array($step, $steps, true);
    }

    protected function finishStep(Request $request, string $step): void
    {
        $steps = $request->getSession()->get('installator.steps', []);

        if (\is_array($steps) === false) {
            $steps = [];
        }

        $steps[] = $step;

        $request->getSession()->set('installator.steps', array_unique($steps));
    }

    protected function resetStep(Request $request, string $step): void
    {
        $steps = $request->getSession()->get('installator.steps', []);

        if (\is_array($steps) && ($key = array_search($step, $steps, true)) !== false) {
            unset($steps[$key]);
        }

        $request->getSession()->set('installator.steps', $steps);
    }
}
