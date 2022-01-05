<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\NodeType\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Validator\CodenameValidator;

/**
 * @author Adam Banaszkiewicz
 */
class NodeTypeForm extends AbstractType
{
    private NodeTypeRegistry $nodeTypeRegistry;

    public function __construct(NodeTypeRegistry $nodeTypeRegistry)
    {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = ['Yes' => 1, 'No' => 0];

        $builder
            ->add('name', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('code', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([new CodenameValidator(), 'validateNodeType']),
                    new Callback([$this, 'validateNodeTypeDuplicate'], null, ['edit_form' => $options['edit_form']]),
                ],
            ])
            ->add('icon', TextType::class, [
                'constraints' => [new NotBlank()],
            ])
            ->add('isRoutable', ChoiceType::class, [
                'choices' => $choices,
                'constraints' => [
                    new NotBlank(),
                    new Choice(['choices' => $choices]),
                ],
            ])
            ->add('isHierarchical', ChoiceType::class, [
                'choices' => $choices,
                'constraints' => [
                    new NotBlank(),
                    new Choice(['choices' => $choices]),
                ],
            ])
            ->add('taxonomyField', TextType::class, [
                'constraints' => [
                    new Callback([$this, 'validateTaxonomyField'], null, ['fields' => $options['fields']]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('csrf_protection', false);
        $resolver->setDefault('edit_form', false);
        $resolver->setRequired('fields');
    }

    public function validateNodeTypeDuplicate(?string $nodeType, ExecutionContextInterface $context, array $payload): void
    {
        if ($payload['edit_form'] === false && $this->nodeTypeRegistry->has($nodeType)) {
            $context->buildViolation('thisNodeTypeIsAlreadyRegistered')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }

    public function validateTaxonomyField(?string $taxonomyFieldName, ExecutionContextInterface $context, array $payload): void
    {
        $isRoutable = $context->getRoot()->get('isRoutable')->getData();

        if (! $isRoutable) {
            return;
        }

        if (! $taxonomyFieldName) {
            $context->buildViolation('pleaseSelectTaxonomyFieldForRoutableNodeType')
                ->setTranslationDomain('content_builder')
                ->atPath('taxonomyField')
                ->addViolation();
        }

        $found = false;

        foreach ($payload['fields'] as $field) {
            if ($field['code'] === $taxonomyFieldName && $field['type'] === 'taxonomy') {
                $found = true;
            }
        }

        if ($found === false) {
            $context->buildViolation('selectedTaxonomyFieldIsNotATaxonomyField')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }
}
