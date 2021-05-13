<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Form\FormType\TaxonomyTypeaheadType;
use Tulia\Cms\Taxonomy\UserInterface\Web\Form\TermForm;
use Tulia\Component\FormBuilder\Extension\AbstractExtension;
use Tulia\Component\FormBuilder\Section\Section;
use Tulia\Component\FormBuilder\Section\SectionsBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeExtension extends AbstractExtension
{
    protected TaxonomyTypeInterface $taxonomyType;

    public function __construct(TaxonomyTypeInterface $taxonomyType)
    {
        $this->taxonomyType = $taxonomyType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->taxonomyType->supports('thumbnail')) {
            $builder->add('thumbnail', FormType\FilepickerType::class, [
                'filter.type' => ['image'],
            ]);
        }

        if ($this->taxonomyType->supports('hierarchy')) {
            $builder->add('parent', TaxonomyTypeaheadType::class, [
                'property_path' => 'parent_id',
                'label'         => false,
                'taxonomy_type' => $this->taxonomyType->getType(),
                'constraints' => [
                    new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                        if ($value === $context->getRoot()->get('id')->getData()) {
                            $context->buildViolation('cannotAssignSelfTermParent')
                                ->setTranslationDomain($this->taxonomyType->getTranslationDomain())
                                ->atPath('constraints')
                                ->addViolation();
                        }
                    }),
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(SectionsBuilderInterface $builder): void
    {
        $sections = [];

        if ($this->taxonomyType->supports('thumbnail')) {
            $builder
                ->add(new Section('lead-image', 'leadImage', '@backend/taxonomy/term/parts/lead-image.tpl'))
                ->setPriority(800)
                ->setGroup('sidebar')
                ->setFields(['thumbnail'])
            ;
        }

        if ($this->taxonomyType->supports('hierarchy')) {
            $builder
                ->rowSection('parent', 'parentTerm', 'parent')
                ->setTranslationDomain($this->taxonomyType->getTranslationDomain())
                ->setPriority(900)
                ->setGroup('sidebar')
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof TermForm && $options['taxonomy_type'] === $this->taxonomyType->getType();
    }
}
