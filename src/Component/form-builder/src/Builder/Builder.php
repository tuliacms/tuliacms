<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Builder;

use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Builder implements BuilderInterface
{
    protected iterable $builders;

    public function __construct(iterable $builders)
    {
        $this->builders = $builders;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ManagerInterface $manager, ?string $group = null, array $options = []): string
    {
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
            'sections' => $manager->getSections($group),
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
