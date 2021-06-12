<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Core;

use Tulia\Cms\ContactForms\Application\FieldType\AbstractType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ConsentType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'consent';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptions(array $options): array
    {
        return [
            'constraints' => $options['constraints'] ?? [],
            'label' => $options['label'] ?? '',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValueFromRequest($value, array $options)
    {
        if (((string) $value) === '1') {
            return $this->translator->trans('yes');
        } else {
            return $this->translator->trans('no');
        }
    }
}
