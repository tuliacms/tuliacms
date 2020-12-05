<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UI\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Node\Application\Model\Node;
use Tulia\Cms\Node\Infrastructure\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\Infrastructure\Framework\Form\FormType\NodeTypeaheadType;
use Tulia\Cms\Node\UI\Web\Form\ScopeEnum;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Form\FormType\TaxonomyTypeaheadType;
use Tulia\Cms\WysiwygEditor\Infrastructure\Framework\Form\FormType\WysiwygEditorType;
use Tulia\Component\FormBuilder\AbstractExtension;
use Tulia\Component\FormBuilder\Section\FormRowSection;
use Tulia\Component\FormBuilder\Section\Section;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeExtension extends AbstractExtension
{
    /**
     * @var NodeTypeInterface
     */
    protected $nodeType;

    /**
     * @param NodeTypeInterface $nodeType
     */
    public function __construct(NodeTypeInterface $nodeType)
    {
        $this->nodeType = $nodeType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->nodeType->supports('introduction')) {
            $builder->add('introduction', Type\TextareaType::class);
        }

        if ($this->nodeType->supports('content')) {
            $builder->add('content', WysiwygEditorType::class, [
                'property_path' => 'content_source'
            ]);
        }

        if ($this->nodeType->supports('thumbnail')) {
            $builder->add('thumbnail', FormType\FilepickerType::class, [
                'filter.type' => ['image'],
            ]);
        }

        if ($this->nodeType->supports('hierarchy')) {
            $builder->add('parent', NodeTypeaheadType::class, [
                'property_path' => 'parent_id',
                'label'         => false,
                'search_route_params' => [
                    'node_type' => $this->nodeType->getType(),
                ],
                'constraints' => [
                    new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                        if ($value === $context->getRoot()->get('id')->getData()) {
                            $context->buildViolation('cannotAssignSelfNodeParent')
                                ->setTranslationDomain($this->nodeType->getTranslationDomain())
                                ->atPath('parent')
                                ->addViolation();
                        }
                    }),
                ],
            ]);
        }

        /**
         * Section for `status` field is created in DefaultFieldsExtension.
         */
        $builder->add('status', Type\ChoiceType::class, [
            'label' => 'publicationStatus',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Choice([ 'choices' => $this->nodeType->getStatuses() ]),
            ],
            'choices' => array_combine($this->nodeType->getStatuses(), $this->nodeType->getStatuses()),
        ]);

        if ($this->nodeType->getRoutableTaxonomy()) {
            $builder->add('category', TaxonomyTypeaheadType::class, [
                'label' => 'category',
                'taxonomy_type' => $this->nodeType->getRoutableTaxonomy(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        $sections = [];

        if ($this->nodeType->supports('introduction')) {
            $sections[] = $section = new Section('introduction', 'introduction', '<div class="container-fluid">
    <div class="row">
        <div class="col">
            {{ form_row(form.introduction) }}
        </div>
    </div>
</div>');
            $section->setPriority(1000);
        }

        if ($this->nodeType->supports('content')) {
            $sections[] = $section = new FormRowSection('content', 'content');
            $section->setPriority(900);
        }

        if ($this->nodeType->supports('thumbnail')) {
            $sections[] = $section = new Section('lead-image', 'leadImage', '@backend/node/parts/lead-image.tpl');
            $section->setPriority(800);
            $section->setGroup('sidebar');
            $section->setFields(['thumbnail']);
        }

        if ($this->nodeType->supports('hierarchy')) {
            $sections[] = $section = new FormRowSection('parent', 'parentNode', 'parent', $this->nodeType->getTranslationDomain());
            $section->setPriority(900);
            $section->setGroup('sidebar');
        }

        if ($this->nodeType->getRoutableTaxonomy()) {
            $sections[] = $section = new FormRowSection('category', 'category', 'category');
            $section->setPriority(900);
            $section->setGroup('sidebar');
        }

        /*foreach ($this->nodeType->getTaxonomies() as $taxonomy) {
            if ($taxonomy['params']['multiple']) {
                $sections[] = $section = new FormRowSection(
                    $taxonomy['taxonomy'],
                    $taxonomy['taxonomy'],
                    [ $taxonomy['taxonomy'], $taxonomy['taxonomy'] . '_additional' ],
                    $this->nodeType->getTranslationDomain()
                );
            } else {
                $sections[] = $section = new FormRowSection(
                    $taxonomy['taxonomy'],
                    $taxonomy['taxonomy'],
                    $taxonomy['taxonomy'],
                    $this->nodeType->getTranslationDomain()
                );
            }

            $section->setPriority(900);
            $section->setGroup('sidebar');
        }*/

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(object $object, string $scope): bool
    {
        return $object instanceof Node && $object->getType() === $this->nodeType->getType() && $scope === ScopeEnum::BACKEND_EDIT;
    }
}
