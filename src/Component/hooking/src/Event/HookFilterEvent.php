<?php

namespace Tulia\Component\Hooking\Event;

class HookFilterEvent extends HookEvent
{
    public function getType(): string
    {
        return 'filter';
    }
}
