<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Menu;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\TypeInterface;
use Tulia\Cms\Menu\UserInterface\Web\Backend\Selector\SelectorInterface;
use Tulia\Cms\Node\Domain\NodeType\NodeTypeRegistryInterface as NodeRegistryInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\MenuItemSelectorForm;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Selector implements SelectorInterface
{
    protected NodeRegistryInterface $nodeTypeRegistry;

    protected EngineInterface $engine;

    protected FormFactoryInterface $formFactory;

    public function __construct(
        NodeRegistryInterface $nodeTypeRegistry,
        EngineInterface $engine,
        FormFactoryInterface $formFactory
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->engine = $engine;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function render(TypeInterface $type, ?string $identity): string
    {
        [, $name] = explode(':', $type->getType());
        $field = 'node_search_' . $name;

        $nodeType = $this->nodeTypeRegistry->getType($name);
        $form = $this->formFactory->create(MenuItemSelectorForm::class, [
            $field => $identity,
        ], [
            'node_type' => $nodeType,
        ]);

        return $this->engine->render(new View('@backend/node/menu/selector.tpl', [
            'form' => $form->createView(),
            'type' => $nodeType->getType(),
            'field' => $field,
            'identityType' => $type,
        ]));
    }
}
