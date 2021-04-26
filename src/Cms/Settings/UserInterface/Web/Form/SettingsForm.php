<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\UserInterface\Web\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType\FilepickerType;
use Tulia\Cms\Platform\Infrastructure\Utilities\DateTime\DateFormatterInterface;
use Tulia\Cms\WysiwygEditor\Core\Application\RegistryInterface;
use Tulia\Cms\WysiwygEditor\Core\Application\WysiwygEditorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsForm extends AbstractType
{
    protected DateFormatterInterface $formatter;
    protected RegistryInterface $editorRegistry;

    public function __construct(
        DateFormatterInterface $formatter,
        RegistryInterface $editorRegistry
    ) {
        $this->formatter      = $formatter;
        $this->editorRegistry = $editorRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateFormats = [
            $this->formatter->format(time(), 'd.m.y') => 'd.m.y',
            $this->formatter->format(time(), 'd.m.Y') => 'd.m.Y',
            $this->formatter->format(time(), 'j F, Y') => 'j F, Y',
            $this->formatter->format(time(), 'H:i d.m.Y') => 'H:i d.m.Y',
            $this->formatter->format(time(), 'H:i, j F Y') => 'H:i, j F Y',
        ];

        $wysiwygEditors = ['---' => 'internal'];

        /** @var WysiwygEditorInterface $editor */
        foreach ($this->editorRegistry->getEditors() as $editor) {
            $wysiwygEditors[$editor->getName()] = $editor->getId();
        }

        unset($wysiwygEditors['Internal']);

        $builder
            ->add('maintenance_mode', Type\ChoiceType::class, [
                'label' => 'maintenanceMode',
                'help' => 'maintenanceModeHelp',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => [
                        'no'  => '0',
                        'yes' => '1',
                    ]]),
                ],
                'choices' => [
                    'no'  => '0',
                    'yes' => '1',
                ],
                'choice_translation_domain' => 'messages',
                'translation_domain' => 'settings',
            ])
            ->add('maintenance_message', Type\TextareaType::class, [
                'label' => 'maintenanceMessage',
                'help' => 'maintenanceMessageHelp',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'translation_domain' => 'settings',
            ])
            ->add('date_format', Type\ChoiceType::class, [
                'label' => 'dateFormat',
                'constraints' => [
                    new Assert\Choice([ 'choices' => $dateFormats ]),
                ],
                'choices' => $dateFormats,
                'choice_translation_domain' => false,
                'translation_domain' => 'settings',
            ])
            ->add('website_name', Type\TextType::class, [
                'label' => 'websiteName',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
                'translation_domain' => 'settings',
            ])
            ->add('website_favicon', FilepickerType::class, [
                'label' => 'websiteFavicon',
                'translation_domain' => 'settings',
            ])
            ->add('administrator_email', Type\TextType::class, [
                'label' => 'administratorEmail',
                'help' => 'administratorEmailHelp',
                'constraints' => [
                    new Assert\Email(),
                    new Assert\NotBlank(),
                ],
                'translation_domain' => 'settings',
            ])
            ->add('wysiwyg_editor', Type\ChoiceType::class, [
                'label' => 'wysiwygEditor',
                'help' => 'wysiwygEditorHelp',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => $wysiwygEditors ]),
                ],
                'choices' => $wysiwygEditors,
                'choice_translation_domain' => false,
                'translation_domain' => 'settings',
                'empty_data' => 'internal',
            ])
            ->add('mail_from_email', Type\TextType::class, [
                'label' => 'mailFromEmail',
                'translation_domain' => 'settings',
                'constraints' => [
                    new Assert\Email(),
                ],
            ])
            ->add('mail_from_name', Type\TextType::class, [
                'label' => 'mailFromName',
                'translation_domain' => 'settings',
            ])
            ->add('mail_transport', Type\ChoiceType::class, [
                'label' => 'mailTransport',
                'constraints' => [
                    new Assert\Choice([ 'choices' => [ 'php', 'smtp', 'sendmail' ] ]),
                ],
                'choices' => [
                    'PHP mail' => 'php',
                    'SMTP' => 'smtp',
                    'sendmail' => 'sendmail',
                ],
                'choice_translation_domain' => false,
                'translation_domain' => 'settings',
            ])
            ->add('mail_host', Type\TextType::class, [
                'label' => 'mailHost',
                'translation_domain' => 'settings',
            ])
            ->add('mail_port', Type\TextType::class, [
                'label' => 'mailPort',
                'translation_domain' => 'settings',
            ])
            ->add('mail_username', Type\TextType::class, [
                'label' => 'mailUsername',
                'translation_domain' => 'settings',
            ])
            ->add('mail_password', Type\PasswordType::class, [
                'label' => 'mailPassword',
                'translation_domain' => 'settings',
            ])
            ->add('mail_encryption', Type\ChoiceType::class, [
                'label' => 'mailEncryption',
                'constraints' => [
                    new Assert\Choice([ 'choices' => [ '', 'ssl', 'tls', 'starttls' ] ]),
                ],
                'choices' => [
                    '---' => '',
                    'SSL' => 'ssl',
                    'TLS' => 'tls',
                    'STARTTLS' => 'starttls',
                ],
                'choice_translation_domain' => false,
                'translation_domain' => 'settings',
            ])
            ->add('mail_sendmailpath', Type\TextType::class, [
                'label' => 'mailSendmailpath',
                'translation_domain' => 'settings',
            ])
            ->add('url_suffix', Type\TextType::class, [
                'label' => 'urlSuffix',
                'translation_domain' => 'settings',
            ]);
    }
}
