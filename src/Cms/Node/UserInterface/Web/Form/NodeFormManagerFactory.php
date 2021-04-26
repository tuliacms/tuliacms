<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Node\Application\Command\NodeStorage;
use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\Model\Node as QueryNode;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeFormManagerFactory
{
    /**
     * @var ManagerFactoryInterface
     */
    private $managerFactory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var NodeStorage
     */
    private $nodeStorage;

    /**
     * @var RegistryInterface
     */
    private $typeRegistry;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param RegistryInterface $typeRegistry
     * @param NodeStorage $nodeStorage
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        RegistryInterface $typeRegistry,
        NodeStorage $nodeStorage
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->typeRegistry   = $typeRegistry;
        $this->nodeStorage    = $nodeStorage;
    }

    public function create(string $nodeType, ?QueryNode $node = null): NodeFormManager
    {
        return new NodeFormManager(
            $this->managerFactory,
            $this->formFactory,
            $this->nodeStorage,
            $this->typeRegistry->getType($nodeType),
            $node
        );
    }
}
