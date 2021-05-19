<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\Node\Infrastructure\Framework\Form\FormType\NodeTypeaheadType;
use Tulia\Cms\Node\Domain\NodeType\NodeTypeInterface;
use Tulia\Cms\Node\UserInterface\Web\Backend\Form\NodeForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Form\FormType\TaxonomyTypeaheadType;
use Tulia\Cms\WysiwygEditor\Core\Infrastructure\Framework\Form\FormType\WysiwygEditorType;
use Tulia\Component\FormSkeleton\Extension\AbstractExtension;
use Tulia\Component\FormSkeleton\Section\SectionsBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeExtension extends AbstractExtension
{
    protected NodeTypeInterface $nodeType;

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
            $builder->add('content', WysiwygEditorType::class);
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
    public function getSections(SectionsBuilderInterface $builder): void
    {
        if ($this->nodeType->supports('introduction')) {
            $builder->add('introduction', [
                'template' => '<div class="container-fluid">
    <div class="row">
        <div class="col">
            {{ form_row(form.introduction) }}
        </div>
    </div>
</div>',
                'priority' => 1000,
            ]);
        }

        if ($this->nodeType->supports('content')) {
            $builder->add('content', [
                'priority' => 900,
            ]);
        }

        if ($this->nodeType->supports('thumbnail')) {
            $builder
                ->add('thumbnail', [
                    'label' => 'leadImage',
                    'view' => '@backend/node/parts/lead-image.tpl',
                    'priority' => 800,
                    'group' => 'sidebar',
                ])
            ;
        }

        if ($this->nodeType->supports('hierarchy')) {
            $builder
                ->add('parent', [
                    'label' => 'parentNode',
                    'translation_domain' => $this->nodeType->getTranslationDomain(),
                    'priority' => 900,
                    'group' => 'sidebar',
                ])
            ;
        }

        if ($this->nodeType->getRoutableTaxonomy()) {
            $builder
                ->add('category', [
                    'priority' => 900,
                    'group' => 'sidebar',
                ])
            ;
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
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof NodeForm && $options['node_type'] === $this->nodeType->getType();
    }
}
