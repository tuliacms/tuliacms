<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\UserInterface\Web\Service\SymfonyFieldBuilder;

/**
 * @author Adam Banaszkiewicz
 */
class RepeatableGroupType extends AbstractType
{
    private SymfonyFieldBuilder $symfonyFieldBuilder;

    public function __construct(
        SymfonyFieldBuilder $symfonyFieldBuilder
    ) {
        $this->symfonyFieldBuilder = $symfonyFieldBuilder;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['fields'] as $field) {
            $this->symfonyFieldBuilder->buildFieldAndAddToBuilder($field, $builder, $options['content_type']);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('repeatable_field', true);

        $resolver->setRequired('content_type');
        $resolver->addAllowedTypes('content_type', ContentType::class);

        $resolver->setRequired('fields');
        $resolver->addAllowedTypes('fields', 'array');
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['repeatable_field'] = true;
        $view->vars['content_type'] = $options['content_type'];
        $view->vars['fields'] = $options['fields'];
        $view->vars['fields_codes'] = array_map(static function ($field) {
            return $field->getCode();
        }, $options['fields']);
    }

    public function getBlockPrefix(): string
    {
        return 'content_builder_repeatable_block';
    }
}
