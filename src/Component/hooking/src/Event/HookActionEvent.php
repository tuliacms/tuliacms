<?php

namespace Tulia\Component\Hooking\Event;

class HookActionEvent extends HookEvent
{
    public function getType(): string
    {
        return 'action';
    }
}
