<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\Model;

use Tulia\Cms\ContentBuilder\Domain\AbstractModel\AbstractContentType;

/**
 * @author Adam Banaszkiewicz
 */
class ContentType extends AbstractContentType
{
    protected string $id;
    protected LayoutType $layout;

    public function __construct(string $id, string $code, string $type, LayoutType $layout, bool $isInternal = false)
    {
        $this->id = $id;
        $this->code = $code;
        $this->type = $type;
        $this->layout = $layout;
        $this->isInternal = $isInternal;
    }

    public static function recreateFromArray(array $data): self
    {
        $layout = new LayoutType($data['layout']['code']);

        foreach ($data['layout']['sections'] as $sectionCode => $section) {
            $groups = [];

            foreach ($section['groups'] as $groupName => $group) {
                $groups[$groupName] = new FieldsGroup($group['code'], $group['name'], (bool) $group['active'], (string) $group['interior'], $group['fields']);
            }

            $layout->addSection(new Section($sectionCode, $groups));
        }

        $self = new self($data['id'], $data['code'], $data['type'], $layout);
        $self->name = $data['name'];
        $self->icon = $data['icon'];
        $self->isRoutable = (bool) $data['is_routable'];
        $self->isHierarchical = (bool) $data['is_hierarchical'];
        $self->routingStrategy = $data['routing_strategy'];
        $self->fields = self::createFields($data['fields']);

        return $self;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return parent::getFields();
    }

    /**
     * @return Field[]
     */
    private static function createFields(array $fields): array
    {
        $result = [];

        foreach ($fields as $field) {
            if ($field['children'] !== []) {
                $field['children'] = self::createFields($field['children']);
            }

            $result[$field['code']] = new Field($field);
        }

        return $result;
    }
}
