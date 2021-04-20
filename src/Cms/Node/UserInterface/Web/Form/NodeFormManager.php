<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Node\Application\Command\NodeStorage;
use Tulia\Cms\Node\Application\Model\Node as ApplicationNode;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Query\Model\Node as QueryNode;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeFormManager
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
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var NodeTypeInterface
     */
    private $nodeType;

    /**
     * @var ApplicationNode
     */
    private $node;

    /**
     * @var QueryNode
     */
    private $sourceNode;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param NodeStorage $nodeStorage
     * @param NodeTypeInterface $nodeType
     * @param QueryNode $sourceNode
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        NodeStorage $nodeStorage,
        NodeTypeInterface $nodeType,
        QueryNode $sourceNode
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->nodeStorage    = $nodeStorage;
        $this->nodeType       = $nodeType;
        $this->sourceNode     = $sourceNode;
    }

    public function createForm(): FormInterface
    {
        $this->node = ApplicationNode::fromQueryModel($this->sourceNode);

        return $this->getManager()->createForm(NodeForm::class, $this->node, ['node_type' => $this->node->getType()]);
    }

    public function save(FormInterface $form): void
    {
        /** @var ApplicationNode $data */
        $data = $form->getData();

        $this->sourceNode->setId($data->getId());

        $this->nodeStorage->save($data);
    }

    public function getNodeType(): NodeTypeInterface
    {
        return $this->nodeType;
    }

    public function getManager(): ManagerInterface
    {
        if ($this->manager) {
            return $this->manager;
        }

        return $this->manager = $this->managerFactory->getInstanceFor($this->node, ScopeEnum::BACKEND_EDIT);
    }
}
