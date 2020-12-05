<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\UI\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\Taxonomy\Application\Model\Term;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Form\FormType\TaxonomyTypeaheadType;
use Tulia\Cms\Taxonomy\UI\Web\Form\ScopeEnum;
use Tulia\Component\FormBuilder\AbstractExtension;
use Tulia\Component\FormBuilder\Section\FormRowSection;
use Tulia\Component\FormBuilder\Section\Section;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyTypeExtension extends AbstractExtension
{
    /**
     * @var TaxonomyTypeInterface
     */
    protected $taxonomyType;

    /**
     * @param TaxonomyTypeInterface $taxonomyType
     */
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
    public function getSections(): array
    {
        $sections = [];

        if ($this->taxonomyType->supports('thumbnail')) {
            $sections[] = $section = new Section('lead-image', 'leadImage', '@backend/taxonomy/term/parts/lead-image.tpl');
            $section->setPriority(800);
            $section->setGroup('sidebar');
            $section->setFields(['thumbnail']);
        }

        if ($this->taxonomyType->supports('hierarchy')) {
            $sections[] = $section = new FormRowSection('parent', 'parentTerm', 'parent', $this->taxonomyType->getTranslationDomain());
            $section->setPriority(900);
            $section->setGroup('sidebar');
        }

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(object $object, string $scope): bool
    {
        return $object instanceof Term && $object->getType() === $this->taxonomyType->getType() && $scope === ScopeEnum::BACKEND_EDIT;
    }
}
