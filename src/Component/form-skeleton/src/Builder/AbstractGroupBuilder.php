<?php

declare(strict_types=1);

namespace Tulia\Component\FormSkeleton\Builder;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractGroupBuilder implements GroupBuilderInterface
{
    protected array $options = [];

    protected array $activeSections = [];

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(string $name, $default = null): array
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function isSectionActive(string $id): bool
    {
        if ($this->activeSections) {
            return \in_array($id, $this->activeSections, true);
        }

        $active = [];

        foreach ($this->getOption('active_first', []) as $default) {
            foreach ($this->getOption('sections', []) as $section) {
                if ($section['id'] === $default) {
                    $active[] = $default;
                    break(2);
                }
            }
        }

        $sections = $this->getOption('sections', []);

        if ($active === [] && isset($sections[0]) && \in_array('_FIRST_', $this->getOption('active_first', []), true)) {
            $active[] = $sections[0]['id'];
        }

        $this->activeSections = array_merge($active, $this->getOption('active', []));

        return \in_array($id, $this->activeSections, true);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function build(array $sections): string;

    /**
     * {@inheritdoc}
     */
    abstract public function supportsGroup(?string $group): bool;
}
