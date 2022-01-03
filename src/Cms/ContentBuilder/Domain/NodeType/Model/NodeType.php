<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Model;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\AbstractContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Exception\MissingRoutableFieldException;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Exception\RoutableFieldIsNotTaxonomyTypeException;

/**
 * @author Adam Banaszkiewicz
 */
class NodeType extends AbstractContentType
{
    protected string $controller = 'Tulia\Cms\Node\UserInterface\Web\Frontend\Controller\Node::show';
    protected string $layout = 'node_default';
    protected string $icon;
    protected ?string $routableTaxonomyField = null;

    public function getRoutableTaxonomyField(): ?string
    {
        return $this->routableTaxonomyField;
    }

    /**
     * @throws MissingRoutableFieldException
     * @throws RoutableFieldIsNotTaxonomyTypeException
     */
    public function setRoutableTaxonomyField(?string $routableTaxonomyField): void
    {
        $this->routableTaxonomyField = $routableTaxonomyField;
    }

    public function getRutableField(): ?Field
    {
        return $this->fields[$this->routableTaxonomyField] ?? null;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @throws RoutableFieldIsNotTaxonomyTypeException
     * @throws MissingRoutableFieldException
     */
    protected function internalValidate(): void
    {
        if ($this->routableTaxonomyField) {
            $this->validateRoutableTaxonomy();
        }
    }

    /**
     * @throws MissingRoutableFieldException
     * @throws RoutableFieldIsNotTaxonomyTypeException
     */
    protected function validateRoutableTaxonomy(): void
    {
        if (isset($this->fields[$this->routableTaxonomyField]) === false) {
            throw MissingRoutableFieldException::fromName($this->code, $this->routableTaxonomyField);
        } elseif ($this->fields[$this->routableTaxonomyField]->getType() !== 'taxonomy') {
            throw RoutableFieldIsNotTaxonomyTypeException::fromName($this->code, $this->routableTaxonomyField);
        }
    }

    protected function internalValidateField(Field $field): void
    {

    }
}
