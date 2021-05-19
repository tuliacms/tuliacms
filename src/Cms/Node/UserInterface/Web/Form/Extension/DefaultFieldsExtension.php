<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Node\UserInterface\Web\Form\NodeForm;
use Tulia\Cms\Node\UserInterface\Web\Form\Transformer\ImmutableDateTimeModelTransformer;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Component\FormSkeleton\Extension\AbstractExtension;
use Tulia\Component\FormSkeleton\Section\SectionsBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultFieldsExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('publishedAt', FormType\DateTimeType::class, [
                'label' => 'publishedAt',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('publishedTo', FormType\DateTimeType::class, [
                'label' => 'publicationEndsAt',
            ])
        ;

        $builder->get('publishedAt')->addModelTransformer(new ImmutableDateTimeModelTransformer());
        $builder->get('publishedTo')->addModelTransformer(new ImmutableDateTimeModelTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(SectionsBuilderInterface $builder): void
    {
        $builder->add('status', [
            'label' => 'statusAndAvailability',
            'view' => '@backend/node/parts/status.tpl',
            'priority' => 1000,
            'group' => 'sidebar',
            'fields' => ['status', 'publishedAt', 'publishedTo'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof NodeForm;
    }
}
