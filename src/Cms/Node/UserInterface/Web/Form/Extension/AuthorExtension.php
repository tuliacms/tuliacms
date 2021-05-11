<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tulia\Cms\Node\UserInterface\Web\Form\NodeForm;
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
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof NodeForm;
    }
}
