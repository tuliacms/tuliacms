<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Form;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\Transformer\NodeIdModelTransformer;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Component\FormSkeleton\Form\AbstractFormSkeletonType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeForm extends AbstractFormSkeletonType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Uuid(),
                ],
            ])
            ->add('title', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('cancel', FormType\CancelType::class, [
                'route' => 'backend.widget',
            ])
            ->add('save', FormType\SubmitType::class)
        ;

        $builder->get('id')->addModelTransformer(new NodeIdModelTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('node_type');
    }
}
