<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UI\Web\Form;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Taxonomy\Application\Command\TermStorage;
use Tulia\Cms\Taxonomy\Application\Model\Term as ApplicationTerm;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term as QueryTerm;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TermFormManager
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
     * @var ManagerInterface
     */
    private $manager;

    /**
     * @var TaxonomyTypeInterface
     */
    private $taxonomyType;

    /**
     * @var ApplicationTerm
     */
    private $term;

    /**
     * @var QueryTerm
     */
    private $sourceTerm;

    /**
     * @param ManagerFactoryInterface $managerFactory
     * @param FormFactoryInterface $formFactory
     * @param TermStorage $termStorage
     * @param TaxonomyTypeInterface $taxonomyType
     * @param QueryTerm $sourceTerm
     */
    public function __construct(
        ManagerFactoryInterface $managerFactory,
        FormFactoryInterface $formFactory,
        TermStorage $termStorage,
        TaxonomyTypeInterface $taxonomyType,
        QueryTerm $sourceTerm
    ) {
        $this->managerFactory = $managerFactory;
        $this->formFactory    = $formFactory;
        $this->termStorage    = $termStorage;
        $this->taxonomyType   = $taxonomyType;
        $this->sourceTerm     = $sourceTerm;
    }

    public function createForm(): FormInterface
    {
        $this->term = ApplicationTerm::fromQueryModel($this->sourceTerm);

        return $this->getManager()->createForm(TermForm::class, $this->term, ['taxonomy_type' => $this->term->getType()]);
    }

    public function save(FormInterface $form): void
    {
        /** @var ApplicationTerm $data */
        $data = $form->getData();

        $this->sourceTerm->setId($data->getId());

        $this->termStorage->save($data);
    }

    public function getTaxonomyType(): TaxonomyTypeInterface
    {
        return $this->taxonomyType;
    }

    public function getManager(): ManagerInterface
    {
        if ($this->manager) {
            return $this->manager;
        }

        return $this->manager = $this->managerFactory->getInstanceFor($this->term, ScopeEnum::BACKEND_EDIT);
    }
}
