<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form;

/**
 * @author Adam Banaszkiewicz
 */
class ScopeEnum
{
    /**
     * Scope used for user forms in backend - for managing all users in user module.
     * is not used for My Account form in backend.
     */
    public const BACKEND_USER = 'backend.user';

    /**
     * Scope used for backend My Account user form only.
     */
    public const BACKEND_MY_ACCOUNT = 'backend.my-account';
}
