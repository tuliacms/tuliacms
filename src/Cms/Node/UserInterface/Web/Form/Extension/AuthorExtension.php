<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Tulia\Cms\Node\Application\Model\Node;
use Tulia\Cms\Node\Query\Factory\NodeFactoryInterface;
use Tulia\Cms\Node\UserInterface\Web\Form\ScopeEnum;
use Tulia\Cms\User\Query\Model\User;
use Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserTypeaheadType;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Component\FormBuilder\AbstractExtension;
use Tulia\Component\FormBuilder\Section\FormRowSection;

/**
 * @author Adam Banaszkiewicz
 */
class AuthorExtension extends AbstractExtension
{
    /**
     * @var AuthenticatedUserProviderInterface
     */
    protected $authenticatedUserProvider;

    /**
     * @param AuthenticatedUserProviderInterface $authenticatedUserProvider
     */
    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $author */
        $author = $this->authenticatedUserProvider->getUser();

        $builder->add('author', UserTypeaheadType::class, [
            'property_path' => 'author_id',
            'empty_data'    => $author->getId(),
            'label'         => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        $sections = [];

        $sections[] = $section = new FormRowSection('author', 'author', 'author');
        $section->setPriority(500);
        $section->setGroup('sidebar');

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(object $object, string $scope): bool
    {
        return $object instanceof Node && $scope === ScopeEnum::BACKEND_EDIT;
    }
}
