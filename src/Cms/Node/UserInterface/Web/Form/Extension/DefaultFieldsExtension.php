<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Node\UserInterface\Web\Form\NodeForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Component\FormBuilder\Extension\AbstractExtension;
use Tulia\Component\FormBuilder\Section\Section;
use Tulia\Component\FormBuilder\Section\SectionsBuilderInterface;

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
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(SectionsBuilderInterface $builder): void
    {
        $builder->add(new Section('status', 'statusAndAvailability', '@backend/node/parts/status.tpl'))
            ->setPriority(1000)
            ->setGroup('sidebar')
            ->setFields(['status', 'publishedAt', 'publishedTo'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof NodeForm;
    }
}
