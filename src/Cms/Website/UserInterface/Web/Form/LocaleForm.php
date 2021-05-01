<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\YesNoType;
use Tulia\Cms\Website\Domain\WriteModel\Model\Locale;
use Tulia\Cms\Website\UserInterface\Web\Form\FormType\LocaleChoiceType;
use Tulia\Component\Routing\Enum\SslModeEnum;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('domain', Type\TextType::class, [
                'label' => 'domain',
                'help' => 'domainHelp',
                'translation_domain' => 'websites',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Callback(function ($object, ExecutionContextInterface $context) {
                        if ($object && filter_var($object, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
                            $context->buildViolation('domainIsInvalid')
                                ->setTranslationDomain('websites')
                                ->atPath('domain')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('domain_development', Type\TextType::class, [
                'label' => 'domainDevelopment',
                'help' => 'domainDevelopmentHelp',
                'translation_domain' => 'websites',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Callback(function ($object, ExecutionContextInterface $context) {
                        if ($object && filter_var($object, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) === false) {
                            $context->buildViolation('domainIsInvalid')
                                ->setTranslationDomain('websites')
                                ->atPath('domain_development')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('path_prefix', Type\TextType::class, [
                'label' => 'pathPrefix',
                'help' => 'pathPrefixHelp',
                'translation_domain' => 'websites',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\/{1}[a-z0-9-_]+$/',
                        'message' => 'websites.pleaseProvideValidPathWithPrecededSlash'
                    ]),
                ],
            ])
            ->add('locale_prefix', Type\TextType::class, [
                'label' => 'localePrefix',
                'help' => 'localePrefixHelp',
                'translation_domain' => 'websites',
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^\/{1}[a-z0-9-_]+$/',
                        'message' => 'websites.pleaseProvideValidPathWithPrecededSlash'
                    ]),
                ],
            ])
            ->add('ssl_mode', Type\ChoiceType::class, [
                'property_path' => 'sslMode',
                'label' => 'sslMode',
                'help' => 'sslModeHelp',
                'constraints' => [
                    new Assert\Choice([ 'choices' =>[
                        'ALLOWED_BOTH'  => SslModeEnum::ALLOWED_BOTH,
                        'FORCE_NON_SSL' => SslModeEnum::FORCE_NON_SSL,
                        'FORCE_SSL'     => SslModeEnum::FORCE_SSL,
                    ]]),
                ],
                'choices' => [
                    'ALLOWED_BOTH'  => SslModeEnum::ALLOWED_BOTH,
                    'FORCE_NON_SSL' => SslModeEnum::FORCE_NON_SSL,
                    'FORCE_SSL'     => SslModeEnum::FORCE_SSL,
                ],
                'translation_domain' => 'websites',
            ])
            ->add('code', LocaleChoiceType::class, [
                'label' => 'locale',
                'help' => 'localeHelp',
                'translation_domain' => 'websites',
                'multiple' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('is_default', YesNoType::class, [
                'label' => 'thisIsADefaultLocale',
                'translation_domain' => 'websites',
                'multiple' => false,
                'required' => true,
            ])
        ;

        $builder->get('is_default')
            ->addModelTransformer(new CallbackTransformer(
                function ($source) {
                    return $source ? '1' : '0';
                },
                function ($rendered) {
                    return $rendered === '1';
                }
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Locale::class,
        ]);
    }
}
