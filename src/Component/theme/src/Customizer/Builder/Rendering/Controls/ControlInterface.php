<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering\Controls;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ControlInterface
{
    public function build(array $params): string;
    public static function getName(): string;

    public function setTranslator(TranslatorInterface $translator): void;
    public function trans(?string $id, array $parameters = [], string $domain = null, string $locale = null): ?string;
}
