<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Menu;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Menu\Application\Selector\SelectorInterface;
use Tulia\Cms\Menu\Infrastructure\Builder\Type\TypeInterface;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface as NodeRegistryInterface;
use Tulia\Cms\Node\UserInterface\Web\Form\MenuItemSelectorForm;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Selector implements SelectorInterface
{
    /**
     * @var NodeRegistryInterface
     */
    protected $nodeTypeRegistry;

    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @param NodeRegistryInterface $nodeTypeRegistry
     * @param EngineInterface $engine
     * @param FormFactoryInterface $formFactory
     */
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
    public function render(TypeInterface $type, string $identity): string
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
