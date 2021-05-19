<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\Taxonomy\Application\Domain\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Form\FormType\TaxonomyTypeaheadType;
use Tulia\Cms\Taxonomy\UserInterface\Web\Backend\Form\TermForm;
use Tulia\Component\FormSkeleton\Extension\AbstractExtension;
use Tulia\Component\FormSkeleton\Section\SectionsBuilderInterface;

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
        if ($this->taxonomyType->supports('thumbnail')) {
            $builder
                ->add('lead-image', [
                    'label' => 'leadImage',
                    'view' => '@backend/taxonomy/term/parts/lead-image.tpl',
                    'priority' => 800,
                    'group' => 'sidebar',
                    'fields' => ['thumbnail'],
                ]);
        }

        if ($this->taxonomyType->supports('hierarchy')) {
            $builder
                ->add('parent', [
                    'label' => 'parentTerm',
                    'translation_domain' => $this->taxonomyType->getTranslationDomain(),
                    'priority' => 900,
                    'group' => 'sidebar',
                ]);
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
