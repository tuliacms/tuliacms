<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\User\UserInterface\Web\Form\MyAccount\MyAccountForm;
use Tulia\Cms\User\UserInterface\Web\Form\UserForm\UserForm;
use Tulia\Component\FormSkeleton\Extension\AbstractExtension;
use Tulia\Component\FormSkeleton\Section\SectionsBuilderInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BasicsExtension extends AbstractExtension
{
    protected TranslatorInterface $translator;

    protected CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        TranslatorInterface $translator,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->translator = $translator;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $translationsSupported = ['en_US', 'pl_PL'];
        $locales = [];

        foreach ($translationsSupported as $locale) {
            $locales[$this->translator->trans('languageName', [ 'code' => $locale ], 'languages')] = $locale;
        }

        $builder
            ->add('name', Type\TextType::class)
            ->add('locale', Type\ChoiceType::class, [
                'label' => 'panelLocale',
                'help' => 'panelLocaleInfo',
                'translation_domain' => 'users',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice(['choices' => $locales]),
                ],
                'choices' => $locales,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(SectionsBuilderInterface $builder): void
    {
        $builder
            ->add('basics', [
                'priority' => 1000,
                'fields' => ['name', 'locale'],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof UserForm || $formType instanceof MyAccountForm;
    }
}
