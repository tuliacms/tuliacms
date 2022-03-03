<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetStylesTypeBuilder implements FieldTypeBuilderInterface
{
    protected ManagerInterface $themeManager;
    private TranslatorInterface $translator;

    public function __construct(ManagerInterface $themeManager, TranslatorInterface $translator)
    {
        $this->themeManager = $themeManager;
        $this->translator = $translator;
    }

    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $theme = $this->themeManager->getTheme();
        $widgetStyles = [];

        if ($theme->hasConfig()) {
            foreach ($theme->getConfig()->getRegisteredWidgetStyles() as $style) {
                $widgetStyles[$this->translator->trans($style['label'], [], $style['translation_domain'])] = $style['name'];
            }
        }

        $options['multiple'] = true;
        $options['choices'] = $widgetStyles;
        $options['choice_translation_domain'] = false;
        $options['translation_domain'] = 'widgets';
        $options['help'] = 'stylesDescription';
        $options['constraints'][] = new Assert\Choice([ 'choices' => $widgetStyles, 'multiple' => true ]);

        return $options;
    }
}
