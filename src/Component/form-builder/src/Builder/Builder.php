<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Builder;

use Symfony\Component\Form\FormView;
use Tulia\Component\FormBuilder\Form\AbstractFormSkeletonType;
use Tulia\Component\FormBuilder\Form\FormSkeletonTypeInterface;
use Tulia\Component\FormBuilder\Form\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    private ManagerInterface $manager;

    private iterable $builders;

    public function __construct(ManagerInterface $manager, iterable $builders)
    {
        $this->builders = $builders;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function build(FormView $form, ?string $group = null, array $options = []): string
    {
        if (! $form->vars['form_type_instance'] instanceof FormSkeletonTypeInterface) {
            throw new \InvalidArgumentException(sprintf(
                'FormTypeInterface must implements %s or extends %s.',
                FormSkeletonTypeInterface::class,
                AbstractFormSkeletonType::class
            ));
        }

        $options = array_merge([
            /**
             * List of active sections to show.
             */
            'active' => [],
            /**
             * List of fallback sections. First found will be showed.
             * When none found, and `_FIRST_` value is in this list,
             * system shows first of the sections.
             */
            'active_first' => [],
            /**
             * Sections list to build. Default is a list from Manager,
             * but it can be overwritted in $options argument.
             */
            'sections' => $this->manager->getSections($form->vars['form_type_instance'], $group),
        ], $options);

        /** @var GroupBuilderInterface $builder */
        foreach ($this->builders as $builder) {
            if ($builder->supportsGroup($group)) {
                $builder->setOptions($options);
                return $builder->build($options['sections']);
            }
        }

        return '';
    }
}
