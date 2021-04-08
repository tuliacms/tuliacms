<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Controls;

use Tulia\Component\DependencyInjection\LazyServiceIterator;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var LazyServiceIterator|ControlInterface[]
     */
    protected $controls;

    /**
     * @param LazyServiceIterator $controls
     */
    public function __construct(LazyServiceIterator $controls)
    {
        $this->controls = $controls;
    }

    /**
     * {@inheritdoc}
     */
    public function build(string $id, string $type, array $params): ?string
    {
        $params['control_name'] = $id;
        $params['control_id']   = $this->createControlId($params['id']);

        foreach ($this->controls as $control) {
            if ($control::getName() === $type) {
                return $control->build($params);
            }
        }

        throw new \OutOfBoundsException(sprintf('Control type "%s" not registered.', $type));
    }

    /**
     * {@inheritdoc}
     */
    public function createControlId($input): string
    {
        return 'control-' . preg_replace('/[^a-zA-Z0-9\-_]+/i', '-', $input);
    }
}
