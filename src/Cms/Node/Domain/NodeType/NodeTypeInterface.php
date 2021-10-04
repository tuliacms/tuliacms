<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType;

use Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node;

/**
 * @author Adam Banaszkiewicz
 */
interface NodeTypeInterface
{
    public const CONTROLLER = Node::class . '::show';

    public function getType(): string;

    public function setType(string $type): void;

    public function getStatuses(): array;

    public function setStatuses(array $statuses): void;

    public function addStatus($status): void;

    public function removeStatus(string $status): void;

    public function hasStatus(string $status): bool;

    public function getIsRoutable(): bool;

    public function isRoutable(): bool;

    public function setIsRoutable(bool $isRoutable): void;

    public function getRoutableTaxonomy(): string;

    public function setRoutableTaxonomy(string $routableTaxonomy): void;

    public function getController(): ?string;

    public function setController(?string $controller): void;

    public function supports(string $name): bool;

    public function getSupports(): array;

    public function setSupports(array $supports): void;

    public function addSupport($support): void;

    public function removeSupport($support): void;

    public function getTranslationDomain(): string;

    public function setTranslationDomain(string $translationDomain): void;

    public function getTaxonomies(): array;

    public function hasTaxonomy(string $name): bool;

    public function setTaxonomies(array $taxonomies): void;

    public function addTaxonomy(string $taxonomy, array $params = []): void;

    public function removeTaxonomy(string $name): void;

    public function setParameter(string $name, $value): void;

    public function getParameter(string $name, $default = null);

    public function getParameters(): array;

    public function setParameters(array $parameters): void;

    public function mergeParameters(array $parameters): void;
}
