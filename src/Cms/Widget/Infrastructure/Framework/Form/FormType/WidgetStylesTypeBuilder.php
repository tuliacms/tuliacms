<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Framework\Form\FormType;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Component\Theme\ManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetStylesTypeBuilder implements FieldTypeBuilderInterface
{
    protected ManagerInterface $themeManager;

    public function __construct(ManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $theme = $this->themeManager->getTheme();
        $widgetStyles = [];

        if ($theme->hasConfig()) {
            $widgetStyles = $theme->getConfig()->getRegisteredWidgetStyles();
            $widgetStyles = array_combine(
                array_map(function ($item) {
                    return $item['label'];
                }, $widgetStyles),
                array_keys($widgetStyles),
            );
        }

        $options['multiple'] = true;
        $options['choices'] = $widgetStyles;
        $options['choice_translation_domain'] = false;
        $options['translation_domain'] = 'widgets';
        $options['help'] = 'stylesDescription';
        $options['constraints'][] = new Assert\Choice([ 'choices' => $widgetStyles ]);

        return $options;
    }
}
