<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Persistence\Dbal\WriteModel;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalPersister
{
    protected ConnectionInterface $connection;
    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->connection     = $connection;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $user): void
    {
        if (! $user['locale']) {
            $user['locale'] = $this->currentWebsite->getLocale()->getCode();
        }

        $mainTable = [];
        $mainTable['id'] = $user['id'];
        $mainTable['password'] = $user['password'];
        $mainTable['email'] = $user['email'];
        $mainTable['locale'] = $user['locale'];
        $mainTable['enabled'] = $user['enabled'] ? 1 : 0;
        $mainTable['account_expired'] = $user['accountExpired'] ? 1 : 0;
        $mainTable['credentials_expired'] = $user['credentialsExpired'] ? 1 : 0;
        $mainTable['account_locked'] = $user['accountLocked'] ? 1 : 0;
        $mainTable['roles'] = json_encode($user['roles']);

        $this->connection->insert('#__user', $mainTable);
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $user): void
    {
        if (! $user['locale']) {
            $user['locale'] = $this->currentWebsite->getLocale()->getCode();
        }

        $mainTable = [];
        $mainTable['id'] = $user['id'];
        $mainTable['email'] = $user['email'];
        $mainTable['locale'] = $user['locale'];
        $mainTable['enabled'] = $user['enabled'] ? 1 : 0;
        $mainTable['account_expired'] = $user['accountExpired'] ? 1 : 0;
        $mainTable['credentials_expired'] = $user['credentialsExpired'] ? 1 : 0;
        $mainTable['account_locked'] = $user['accountLocked'] ? 1 : 0;
        $mainTable['roles'] = json_encode($user['roles']);

        // Update password only if exists. Empty password means user don't want to update
        // and user cannot have empty passwords.
        if (isset($user['password']) && $user['password']) {
            $mainTable['password'] = $user['password'];
        }

        $this->connection->update('#__user', $mainTable, ['id' => $user['id']]);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $user): void
    {
        $this->connection->delete('#__user', ['id' => $user['id']]);
    }
}
