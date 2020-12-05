<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\TypeaheadType;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\MetadataEnum;
use Tulia\Cms\User\Query\FinderFactoryInterface;
use Tulia\Cms\User\Query\Enum\ScopeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class UserTypeaheadType extends AbstractType
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(FinderFactoryInterface $finderFactory)
    {
        $this->finderFactory = $finderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'search_route'  => 'backend.user.search.typeahead',
            'display_prop'  => 'username',
            'data_provider_single' => function (array $criteria): ?array {
                $user = $this->finderFactory->getInstance(ScopeEnum::INTERNAL)->find($criteria['value']);

                if ($user === null) {
                    return null;
                }

                $username = $user->getUsername();

                if ($user->getMeta(MetadataEnum::NAME)) {
                    $username = $user->getMeta(MetadataEnum::NAME) . " ({$username})";
                }

                return ['username' => $username];
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TypeaheadType::class;
    }
}
