<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleType extends AbstractType
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // @todo Get available locales from some centralized place in system
        $translationsSupported = ['en_US', 'pl_PL'];
        $locales = [];

        foreach ($translationsSupported as $locale) {
            $locales[$this->translator->trans('languageName', [ 'code' => $locale ], 'languages')] = $locale;
        }

        $resolver->setDefault('choices', $locales);
        $resolver->setDefault('constraints', [
            new Assert\Choice(['choices' => $locales]),
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
