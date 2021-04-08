<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UI\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\User\Application\Model\User;
use Tulia\Component\FormBuilder\AbstractExtension;
use Tulia\Component\FormBuilder\Section\FormRowSection;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractBasicsExtension extends AbstractExtension
{
    /**
     * @var array
     */
    protected $scopes = [];

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param TranslatorInterface $translator
     * @param CurrentWebsiteInterface $currentWebsite
     * @param array $scopes
     */
    public function __construct(
        TranslatorInterface $translator,
        CurrentWebsiteInterface $currentWebsite,
        array $scopes
    ) {
        $this->translator     = $translator;
        $this->currentWebsite = $currentWebsite;
        $this->scopes         = $scopes;
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
    public function getSections(): array
    {
        $sections = [];

        $sections[] = $section = new FormRowSection('basics', 'basics', ['name', 'locale']);
        $section->setPriority(1000);

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(object $object, string $scope): bool
    {
        return $object instanceof User && \in_array($scope, $this->scopes, true);
    }
}
