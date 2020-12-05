<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\UI\Web\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Routing\Website\Locale\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleChoiceType extends AbstractType
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param StorageInterface $storage
     * @param TranslatorInterface $translator
     */
    public function __construct(StorageInterface $storage, TranslatorInterface $translator)
    {
        $this->storage    = $storage;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $source = $this->storage->all();

        $locales = [];

        foreach ($source as $locale) {
            $locales[$this->translator->trans('languageName', [ 'code' => $locale->getCode() ], 'languages') . ' [' . $locale->getCode() . ']'] = $locale->getCode();
        }

        $resolver->setDefaults([
            'choices' => $locales,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
