<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Taxonomy\Application\Command\TermStorage;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term as QueryTerm;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TermFormManagerFactory
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
     * @var TermStorage
     */
    private $termStorage;

    /**
     * @var RegistryInterface
     */
    private $typeRegistry;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param RegistryInterface $typeRegistry
     * @param TermStorage $termStorage
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        RegistryInterface $typeRegistry,
        TermStorage $termStorage
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->typeRegistry   = $typeRegistry;
        $this->termStorage    = $termStorage;
    }

    public function create(string $termType, ?QueryTerm $term = null): TermFormManager
    {
        return new TermFormManager(
            $this->managerFactory,
            $this->formFactory,
            $this->termStorage,
            $this->typeRegistry->getType($termType),
            $term
        );
    }
}
