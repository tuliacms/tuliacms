<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Core;

use Tulia\Cms\ContactForms\Application\FieldType\AbstractType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CheckboxType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'checkbox';
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
    public function prepareValueFromRequest($value, array $options)
    {
        if (((string) $value) === '1') {
            return $this->translator->trans('yes');
        } else {
            return $this->translator->trans('no');
        }
    }
}
