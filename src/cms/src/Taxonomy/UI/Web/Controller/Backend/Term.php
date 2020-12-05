<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UI\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Taxonomy\Application\Command\TermStorage;
use Tulia\Cms\Taxonomy\Application\Exception\TranslatableTermException;
use Tulia\Cms\Taxonomy\Application\Model\Term as ApplicationNode;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Query\CriteriaBuilder\RequestCriteriaBuilder;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\Exception\MultipleFetchException;
use Tulia\Cms\Taxonomy\Query\Exception\QueryException;
use Tulia\Cms\Taxonomy\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Taxonomy\Query\Factory\TermFactoryInterface;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term as QueryTerm;
use Tulia\Cms\Taxonomy\UI\Web\Form\TermFormManagerFactory;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Kernel\Exception\NotFoundHttpException;
use Tulia\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Term extends AbstractController
{
    /**
     * @var RegistryInterface
     */
    protected $taxonomyRegistry;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var TermStorage
     */
    protected $termStorage;

    /**
     * @param RegistryInterface $taxonomyRegistry
     * @param FinderFactoryInterface $finderFactory
     * @param TermStorage $termStorage
     */
    public function __construct(
        RegistryInterface $taxonomyRegistry,
        FinderFactoryInterface $finderFactory,
        TermStorage $termStorage
    ) {
        $this->taxonomyRegistry = $taxonomyRegistry;
        $this->finderFactory    = $finderFactory;
        $this->termStorage      = $termStorage;
    }

    /**
     * @param Request $request
     * @param string $taxonomyType
     * @param string|null $list
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    public function list(Request $request, string $taxonomyType, string $list = null)
    {
        if ($list !== 'list') {
            return $this->redirect(
                'backend.term',
                array_merge(
                    ['list' => 'list', 'taxonomy_type' => $taxonomyType],
                    $request->query->all()
                )
            );
        }

        $criteria = (new RequestCriteriaBuilder($request))->build([ 'taxonomy_type' => $taxonomyType ]);
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_LISTING);
        $finder->setCriteria($criteria);
        $finder->fetchRaw();

        return $this->view('@backend/taxonomy/term/list.tpl', [
            'terms'        => $finder->getResult(),
            'taxonomyType' => $this->findTaxonomyType($criteria['taxonomy_type']),
            'criteria'     => $criteria,
            'paginator'    => $finder->getPaginator($request),
        ]);
    }

    /**
     * @param Request $request
     * @param string $taxonomyType
     * @param TermFormManagerFactory $formFactory
     * @param TermFactoryInterface $termFactory
     *
     * @return RedirectResponse|ViewInterface
     *
     * @CsrfToken(id="term_form")
     */
    public function create(
        Request $request,
        string $taxonomyType,
        TermFormManagerFactory $formFactory,
        TermFactoryInterface $termFactory
    ) {
        $term = $termFactory->createNew([
            'type' => $taxonomyType,
            'visibility' => true,
        ]);
        $manager = $formFactory->create($taxonomyType, $term);
        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('termSaved', [], $manager->getTaxonomyType()->getTranslationDomain()));
            return $this->redirect('backend.term.edit', [ 'id' => $term->getId(), 'taxonomy_type' => $manager->getTaxonomyType()->getType() ]);
        }

        return $this->view('@backend/taxonomy/term/create.tpl', [
            'manager' => $manager->getManager(),
            'taxonomyType' => $manager->getTaxonomyType(),
            'term' => $term,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param TermFormManagerFactory $factory
     * @param string $taxonomyType
     * @param string $id
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     *
     * @CsrfToken(id="term_form")
     */
    public function edit(
        Request $request,
        TermFormManagerFactory $factory,
        string $taxonomyType,
        string $id
    ) {
        $term = $this->getTermById($id);
        $manager = $factory->create($taxonomyType, $term);
        $form = $manager->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->save($form);

            $this->setFlash('success', $this->trans('termSaved', [], $manager->getTaxonomyType()->getTranslationDomain()));
            return $this->redirect('backend.term.edit', [ 'id' => $term->getId(), 'taxonomy_type' => $manager->getTaxonomyType()->getType() ]);
        }

        return $this->view('@backend/taxonomy/term/edit.tpl', [
            'manager' => $manager->getManager(),
            'taxonomyType' => $manager->getTaxonomyType(),
            'term' => $term,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     *
     * @CsrfToken(id="node.delete")
     */
    public function delete(Request $request): RedirectResponse
    {
        $taxonomyType = $this->findTaxonomyType($request->query->get('taxonomy_type', 'categiry'));
        $removedNodes = 0;

        foreach ($request->request->get('ids') as $id) {
            try {
                $term = $this->getTermById($id);
            } catch (NotFoundHttpException $e) {
                continue;
            }

            try {
                $this->termStorage->delete(ApplicationNode::fromQueryModel($term));
                $removedNodes++;
            } catch (TranslatableTermException $e) {
                $this->setFlash('warning', $this->transObject($e));
            }
        }

        if ($removedNodes) {
            $this->setFlash('success', $this->trans('selectedNodesWereDeleted', [], $taxonomyType->getTranslationDomain()));
        }

        return $this->redirect('backend.term', [ 'taxonomy_type' => $taxonomyType->getType() ]);
    }

    /**
     * @param string $type
     *
     * @return TaxonomyTypeInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findTaxonomyType(string $type): TaxonomyTypeInterface
    {
        $taxonomyType = $this->taxonomyRegistry->getType($type);

        if (! $taxonomyType) {
            throw $this->createNotFoundException('Taxonomy type not found.');
        }

        return $taxonomyType;
    }

    /**
     * @param string $id
     *
     * @return QueryTerm
     *
     * @throws MultipleFetchException
     * @throws NotFoundHttpException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getTermById(string $id): QueryTerm
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::BACKEND_SINGLE);
        $finder->setCriteria(['id' => $id]);
        $finder->fetchRaw();

        $term = $finder->getResult()->first();

        if (! $term) {
            throw $this->createNotFoundException($this->trans('termNotFound'));
        }

        return $term;
    }
}
