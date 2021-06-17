<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Ports\Domain\ReadModel;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormFinderScopeEnum
{
    /**
     * Frontend, single form.
     */
    public const SINGLE = 'single';

    public const INTERNAL = 'internal';

    public const SEARCH = 'search';
}
