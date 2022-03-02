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
class WidgetSpaceTypeBuilder implements FieldTypeBuilderInterface
{
    protected ManagerInterface $themeManager;

    public function __construct(ManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $theme = $this->themeManager->getTheme();
        $spaces = [];

        if ($theme->hasConfig()) {
            $spaces = $theme->getConfig()->getRegisteredWidgetSpaces();
            $spaces = array_combine(
                array_map(function ($item) {
                    return $item['label'];
                }, $spaces),
                array_map(function ($item) {
                    return $item['name'];
                }, $spaces),
            );
        }

        $options['choices'] = $spaces;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $spaces ]);

        return $options;
    }
}
