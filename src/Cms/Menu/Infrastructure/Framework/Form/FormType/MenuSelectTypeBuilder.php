<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Adam Banaszkiewicz
 */
class MenuSelectTypeBuilder implements FieldTypeBuilderInterface
{
    private MenuFinderInterface $menuFinder;

    public function __construct(MenuFinderInterface $menuFinder)
    {
        $this->menuFinder = $menuFinder;
    }

    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $source = $this->menuFinder->find([], MenuFinderScopeEnum::INTERNAL);
        $menus = [];

        foreach ($source as $item) {
            $menus[$item->getName()] = $item->getId();
        }

        $options['choices'] = $menus;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\Uuid();
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $menus ]);

        return $options;
    }
}
