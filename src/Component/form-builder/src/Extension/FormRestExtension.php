<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Extension;

use Symfony\Component\Form\FormTypeInterface;
use Tulia\Component\FormBuilder\AbstractExtension;
use Tulia\Component\FormBuilder\Section\FormRestSection;

/**
 * @author Adam Banaszkiewicz
 */
class FormRestExtension extends AbstractExtension
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $translationDomain;

    /**
     * @param string|null $id
     * @param string|null $label
     * @param string|null $translationDomain
     */
    public function __construct(string $id = null, string $label = null, string $translationDomain = null)
    {
        $this->id = $id ?? 'form-rest';
        $this->label = $label ?? 'otherSettings';
        $this->translationDomain = $translationDomain ?? 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        $sections = [];

        $sections[] = $section = new FormRestSection($this->id, $this->label, $this->translationDomain);
        $section->setPriority(-1000);

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return true;
    }
}
