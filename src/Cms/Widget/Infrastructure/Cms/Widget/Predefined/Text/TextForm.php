<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Text;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Tulia\Cms\WysiwygEditor\Core\Infrastructure\Framework\Form\FormType\WysiwygEditorType;

/**
 * @author Adam Banaszkiewicz
 */
class TextForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', WysiwygEditorType::class);
    }
}
