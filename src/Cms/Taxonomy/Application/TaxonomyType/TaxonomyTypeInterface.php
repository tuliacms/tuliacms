<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\TaxonomyType;

use Tulia\Cms\Taxonomy\UserInterface\Web\Controller\Frontend\Term;

/**
 * @author Adam Banaszkiewicz
 */
interface TaxonomyTypeInterface
{
    public const CONTROLLER = Term::class . '::show';

    public function getType(): string;
    public function getController(): ?string;
    public function isRoutable(): bool;
    public function getRoutingStrategy(): string;
    public function setRoutingStrategy(string $name): void;
    public function supports(string $name): bool;
    public function getTranslationDomain(): string;
    public function getParameter(string $name, $default = null);
    public function getParameters(): array;
}
