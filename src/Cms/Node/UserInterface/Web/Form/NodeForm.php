<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface as NodeTypeRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Component\FormSkeleton\Form\AbstractFormSkeletonType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeForm extends AbstractFormSkeletonType
{
    protected NodeTypeRegistry $nodesRegistry;

    public function __construct(NodeTypeRegistry $nodesRegistry)
    {
        $this->nodesRegistry = $nodesRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var NodeTypeInterface $nodeType */
        $nodeType = $this->nodesRegistry->getType($options['node_type']);

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
                    new Assert\Choice([ 'choices' => [ $nodeType->getType() ] ]),
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

        if ($nodeType->isRoutable()) {
            $builder->add('slug', Type\TextType::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('node_type');
    }
}
