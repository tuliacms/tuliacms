<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Enum;

/**
 * @author Adam Banaszkiewicz
 */
class ChangesetTypeEnum
{
    /**
     * Current, active theme changeset. Used in production.
     * This type of changeset must be ONLY ONE for one theme!
     */
    public const ACTIVE = 'active';

    /**
     * Temporary changesets. Used in working progress changes.
     * This type of changeset can be multiple for one theme.
     */
    public const TEMPORARY = 'temporary';

    /**
     * Predefined changeset. Used for theme providers and system administrators
     * to save previously defined changesets to future usage.
     * This type of changeset can be multiple for one theme.
     */
    public const PREDEFINED = 'predefined';
}
