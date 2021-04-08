<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Extension\ProfilerExtension;
use Twig\Loader\LoaderInterface;
use Twig\Profiler\Profile;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Factory
{
    public static function factory(
        LoaderInterface $loader,
        RuntimeLoaderInterface $runtimeLoader,
        Profile $profile,
        bool $debug
    ): Environment {
        $twig = new Environment($loader, [
            'debug' => $debug,
            'strict_variables' => $debug,
        ]);
        $twig->addRuntimeLoader($runtimeLoader);

        if ($debug) {
            $twig->addExtension(new ProfilerExtension($profile));
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    }
}
