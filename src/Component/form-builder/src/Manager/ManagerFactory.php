<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Manager;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Component\FormBuilder\RegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ManagerFactory implements ManagerFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param FormFactoryInterface $formFactory
     * @param RegistryInterface $registry
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RegistryInterface $registry
    ) {
        $this->formFactory = $formFactory;
        $this->registry    = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstanceFor(object $object): ManagerInterface
    {
        return new Manager(
            $this->formFactory,
            $this->registry->getSupportive($object, $scope)
        );
    }
}
