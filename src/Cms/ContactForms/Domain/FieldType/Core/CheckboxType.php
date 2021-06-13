<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\FieldType\Core;

use Tulia\Cms\ContactForms\Ports\Domain\FieldType\AbstractFieldType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CheckboxType extends AbstractFieldType
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
