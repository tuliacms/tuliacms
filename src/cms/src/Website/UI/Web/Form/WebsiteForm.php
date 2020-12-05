<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UI\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Tulia\Cms\Website\Application\Model\Locale;
use Tulia\Cms\Website\Application\Model\LocaleCollection;

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
            ->add('backend_prefix', Type\TextType::class, [
                'label' => 'backendPrefix',
                'help' => 'backendPrefixHelp',
                'translation_domain' => 'websites',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^\/{1}[a-z0-9-_]+$/',
                        'message' => 'websites.pleaseProvideValidPathWithPrecededSlash'
                    ]),
                ]
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

    public function validateDefaultLocale(LocaleCollection $locales, ExecutionContextInterface $context): void
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

    public function validateDoubledLocale(LocaleCollection $locales, ExecutionContextInterface $context): void
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
