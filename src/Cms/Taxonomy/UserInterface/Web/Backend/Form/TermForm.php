<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Domain\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\ModelTransformer\TermIdModelTransformer;
use Tulia\Component\FormSkeleton\Form\AbstractFormSkeletonType;

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
            ->add('name', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('cancel', FormType\CancelType::class, [
                'route' => 'backend.term',
                'route_params' => [
                    'taxonomyType' => $taxonomyType->getType(),
                ],
            ])
            ->add('save', FormType\SubmitType::class)
        ;

        if ($taxonomyType->isRoutable()) {
            $builder->add('slug', Type\TextType::class);
        }

        $builder->get('id')->addModelTransformer(new TermIdModelTransformer());
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
