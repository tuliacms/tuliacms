<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Node\Domain\NodeFlag\NodeFlagRegistryInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\NodeForm;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\Transformer\ImmutableDateTimeModelTransformer;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Component\FormSkeleton\Extension\AbstractExtension;
use Tulia\Component\FormSkeleton\Section\SectionsBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultFieldsExtension extends AbstractExtension
{
    private NodeFlagRegistryInterface $flagRegistry;

    private TranslatorInterface $translator;

    public function __construct(NodeFlagRegistryInterface $flagRegistry, TranslatorInterface $translator)
    {
        $this->flagRegistry = $flagRegistry;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $availableFlags = [];

        foreach ($this->flagRegistry->all() as $type => $flag) {
            $availableFlags[$this->translator->trans($flag['label'])] = $type;
        }

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
            ->add('flags', ChoiceType::class, [
                'label' => 'flags',
                'help' => 'flagsHelp',
                'choices' => $availableFlags,
                'choice_translation_domain' => false,
                'multiple' => true,
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
        $builder->add('flags', [
            'label' => 'flags',
            'view' => '@backend/node/parts/flags.tpl',
            'priority' => 0,
            'group' => 'sidebar',
            'fields' => ['flags'],
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
