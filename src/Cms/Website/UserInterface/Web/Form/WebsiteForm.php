<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\Website\Domain\WriteModel\Model\Locale;
use Tulia\Cms\Website\UserInterface\Web\Form\Transformer\WebsiteIdModelTransformer;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteForm extends AbstractType
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
            ->add('name', Type\TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('active', Type\ChoiceType::class, [
                'required' => true,
                'choices' => [
                    'Yes' => 1,
                    'No' => 0,
                ],
                'constraints' => [
                    new Assert\Range(['min' => 0, 'max' => 1]),
                ],
            ])
            ->add('locales', CollectionType::class, [
                'entry_type' => LocaleForm::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'constraints' => [
                    new Assert\Callback(['callback' => [$this, 'validateDefaultLocale']]),
                    new Assert\Callback(['callback' => [$this, 'validateDoubledLocale']]),
                ],
            ])
        ;

        if ($options['append_buttons']) {
            $builder
                ->add('cancel', FormType\CancelType::class, [
                    'route' => 'backend.website',
                ])
                ->add('save', FormType\SubmitType::class)
            ;
        }

        $builder->get('id')->addModelTransformer(new WebsiteIdModelTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'append_buttons' => true,
        ]);
    }

    public function validateDefaultLocale(array $locales, ExecutionContextInterface $context): void
    {
        $countDefaults = 0;

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            if ($locale->isDefault()) {
                $countDefaults++;
            }
        }

        if ($countDefaults === 0) {
            $context->buildViolation('websites.pleaseSelectDefaultLocale')
                ->setTranslationDomain('validators')
                ->addViolation();
        } elseif ($countDefaults > 1) {
            $context->buildViolation('websites.thereCanBeOnlyOneDefaultLocaleButSelected', ['count' => $countDefaults])
                ->setTranslationDomain('validators')
                ->addViolation();
        }
    }

    public function validateDoubledLocale(array $locales, ExecutionContextInterface $context): void
    {
        $codes = [];

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            if (\in_array($locale->getCode(), $codes, true)) {
                $context->buildViolation('websites.detectedDoubledLocale', ['code' => $locale->getCode()])
                    ->setTranslationDomain('validators')
                    ->addViolation();
            }

            $codes[] = $locale->getCode();
        }
    }
}
