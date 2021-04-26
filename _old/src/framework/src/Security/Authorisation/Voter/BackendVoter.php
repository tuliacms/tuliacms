<?php

declare(strict_types=1);

namespace Tulia\Framework\Security\Authorisation\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BackendVoter implements VoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function vote(TokenInterface $token, $subject, array $attributes)
    {
        if (\in_array('BACKEND', $attributes, true) === false) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (
            $token->isAuthenticated()
            && \in_array('ROLE_ADMIN', $token->getRoleNames(), true)
        ) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
