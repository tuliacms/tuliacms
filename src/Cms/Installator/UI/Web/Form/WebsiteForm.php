<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\UI\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Website\UI\Web\Form\FormType\LocaleChoiceType;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WebsiteForm extends AbstractType
{
    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

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
        ;
    }
}
