<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Form;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Component\FormBuilder\Form\AbstractFormSkeletonType;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TermForm extends AbstractFormSkeletonType
{
    protected RegistryInterface $taxonomyRegistry;

    public function __construct(RegistryInterface $taxonomyRegistry)
    {
        $this->taxonomyRegistry = $taxonomyRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var TaxonomyTypeInterface $taxonomyType */
        $taxonomyType = $this->taxonomyRegistry->getType($options['taxonomy_type']);

        $builder
            ->add('id', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Uuid(),
                ],
            ])
            ->add('type', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => [ $taxonomyType->getType() ] ]),
                ],
            ])
            ->add('name', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('cancel', FormType\CancelType::class, [
                'route' => 'backend.term',
                'route_params' => [
                    'taxonomy_type' => $taxonomyType->getType(),
                ],
            ])
            ->add('save', FormType\SubmitType::class)
        ;

        if ($taxonomyType->isRoutable()) {
            $builder->add('slug', Type\TextType::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'taxonomy_type' => 'category',
        ]);

        $resolver->setRequired('taxonomy_type');
    }
}
