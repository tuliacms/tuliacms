<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Controls;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractControl implements ControlInterface
{
    protected $params = [];

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $params): void
    {
        $this->params = $params;

        if (isset($this->params['value']) === false) {
            $this->params['value'] = null;
        }

        if ($this->params['value'] === null) {
            $this->params['value'] = $this->params['default'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->params[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function escapeAttribute($input)
    {
        if ($input === null || \is_int($input)) {
            return $input;
        }

        return htmlspecialchars($input ?? '');
    }
}
