<?php

namespace Tulia\Component\Hooking\Controller;

trait HookingControllerTrait
{
    public function getHooker()
    {
        return $this->container->get('hooker');
    }

    public function registerAction($action, callable $callable, $priority = 0)
    {
        return $this->container->get('hooker')->registerAction($action, $callable, $priority);
    }

    public function registerFilter($filter, callable $callable, $priority = 0)
    {
        return $this->container->get('hooker')->registerFilter($filter, $callable, $priority);
    }

    public function doAction($action, array $arguments = [])
    {
        return $this->container->get('hooker')->doAction($action, $arguments);
    }

    public function doFilter($filter, $content = null, array $arguments = [])
    {
        return $this->container->get('hooker')->doFilter($filter, $content, $arguments);
    }
}
