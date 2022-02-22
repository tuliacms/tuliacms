<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;

/**
 * @author Adam Banaszkiewicz
 */
class UserAvatarBuilder implements FieldTypeBuilderInterface
{
    public function build(Field $field, array $options, ContentType $contentType): array
    {
        $options['constraints'][] = new Assert\Image([
            'minWidth' => 100,
            'minHeight' => 100,
            'maxWidth' => 700,
            'maxHeight' => 700,
            'allowLandscape' => false,
            'allowPortrait' => false,
            'mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
        ]);

        return $options;
    }
}
