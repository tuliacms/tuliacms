<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\UserInterface\Web\Shared\Form\FormType;

use Tulia\Cms\ContactForm\Ports\Domain\ReadModel\ContactFormFinderInterface;
use Tulia\Cms\ContactForm\Ports\Domain\ReadModel\ContactFormFinderScopeEnum;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class FormSelectorBuilder implements FieldTypeBuilderInterface
{
    private ContactFormFinderInterface $finder;

    public function __construct(ContactFormFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $forms = $this->finder->find([], ContactFormFinderScopeEnum::SEARCH);

        foreach ($forms as $form) {
            $options['choices'][$form->getName()] = $form->getId();
        }

        return $options;
    }
}
