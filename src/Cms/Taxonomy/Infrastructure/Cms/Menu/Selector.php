<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Menu;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Menu\Application\Selector\SelectorInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\TypeInterface;
use Tulia\Cms\Taxonomy\Application\taxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\UserInterface\Web\Form\MenuItemSelectorForm;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Selector implements SelectorInterface
{
    protected RegistryInterface $typeRegistry;
    protected EngineInterface $engine;
    protected FormFactoryInterface $formFactory;

    public function __construct(
        RegistryInterface $typeRegistry,
        EngineInterface $engine,
        FormFactoryInterface $formFactory
    ) {
        $this->typeRegistry = $typeRegistry;
        $this->engine = $engine;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function render(TypeInterface $type, string $identity): string
    {
        [, $name] = explode(':', $type->getType());
        $field = 'term_search_' . $name;

        $taxonomyType = $this->typeRegistry->getType($name);
        $form = $this->formFactory->create(MenuItemSelectorForm::class, [
            $field => $identity,
        ], [
            'taxonomy_type' => $taxonomyType,
        ]);

        return $this->engine->render(new View('@backend/taxonomy/menu/selector.tpl', [
            'form' => $form->createView(),
            'type' => $taxonomyType->getType(),
            'field' => $field,
            'identityType' => $type,
        ]));
    }
}
