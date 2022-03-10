<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\Menu\Domain\Builder\Type\RegistryInterface;
use Tulia\Cms\Menu\Domain\Builder\Type\TypeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemTypeBuilder implements FieldTypeBuilderInterface
{
    protected RegistryInterface $registry;
    protected TranslatorInterface $translator;

    public function __construct(
        RegistryInterface $registry,
        TranslatorInterface $translator
    ) {
        $this->registry = $registry;
        $this->translator = $translator;
    }

    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $itemTypes = [];

        /** @var TypeInterface $type */
        foreach ($this->registry->all() as $type) {
            $itemTypes[$this->translator->trans($type->getLabel(), [], $type->getTranslationDomain())] = $type->getType();
        }

        $options['choices'] = $itemTypes;
        $options['choice_translation_domain'] = false;
        $options['constraints'][] = new Assert\NotBlank();
        $options['constraints'][] = new Assert\Choice([ 'choices' => $itemTypes ]);

        return $options;
    }
}
