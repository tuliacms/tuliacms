<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Controls;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractControl implements ControlInterface
{
    protected array $params = [];
    protected TranslatorInterface $translator;

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null): ?string
    {
        return $this->translator->trans($id, $parameters, $domain, $locale);
    }

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
