<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\ContentType\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Form\Validator\CodenameValidator;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeForm extends AbstractType
{
    private ContentTypeRegistry $contentTypeRegistry;

    public function __construct(ContentTypeRegistry $contentTypeRegistry)
    {
        $this->contentTypeRegistry = $contentTypeRegistry;
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
            ->add('icon', TextType::class)
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
            ->add('routingStrategy', TextType::class)
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
        if ($payload['edit_form'] === false && $this->contentTypeRegistry->has($nodeType)) {
            $context->buildViolation('thisContentTypeIsAlreadyRegistered')
                ->setTranslationDomain('content_builder')
                ->addViolation();
        }
    }
}
