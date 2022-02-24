<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserTypeahead;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\TypeaheadType;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderInterface;
use Tulia\Cms\User\Domain\ReadModel\Finder\UserFinderScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class UserTypeaheadType extends AbstractType
{
    protected UserFinderInterface $userFinder;

    public function __construct(UserFinderInterface $userFinder)
    {
        $this->userFinder = $userFinder;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_route'  => 'backend.user.search.typeahead',
            'display_prop'  => 'username',
            'data_provider_single' => function (array $criteria): ?array {
                $user = $this->userFinder->findOne(['id' => $criteria['value']], UserFinderScopeEnum::INTERNAL);

                if ($user === null) {
                    return null;
                }

                $username = $user->getEmail();

                if ($user->attribute('name')) {
                    $username = $user->attribute('name') . " ({$username})";
                }

                return ['username' => $username];
            },
        ]);
    }

    public function getParent(): string
    {
        return TypeaheadType::class;
    }
}
