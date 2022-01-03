<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Frontend\Menu;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\TaxonomyTypeRegistry;
use Tulia\Cms\Menu\Domain\Builder\Type\TypeInterface;
use Tulia\Cms\Menu\UserInterface\Web\Backend\Selector\SelectorInterface;
use Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\MenuItemSelectorForm;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Selector implements SelectorInterface
{
    protected TaxonomyTypeRegistry $taxonomyTypeRegistry;

    protected EngineInterface $engine;

    protected FormFactoryInterface $formFactory;

    public function __construct(
        TaxonomyTypeRegistry $taxonomyTypeRegistry,
        EngineInterface $engine,
        FormFactoryInterface $formFactory
    ) {
        $this->taxonomyTypeRegistry = $taxonomyTypeRegistry;
        $this->engine = $engine;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function render(TypeInterface $type, ?string $identity): string
    {
        [, $name] = explode(':', $type->getType());
        $field = 'term_search_' . $name;

        $taxonomyType = $this->taxonomyTypeRegistry->get($name);
        $form = $this->formFactory->create(MenuItemSelectorForm::class, [
            $field => $identity,
        ], [
            'taxonomy_type' => $taxonomyType,
        ]);

        return $this->engine->render(new View('@backend/taxonomy/menu/selector.tpl', [
            'form' => $form->createView(),
            'type' => $taxonomyType->getCode(),
            'field' => $field,
            'identityType' => $type,
        ]));
    }
}
