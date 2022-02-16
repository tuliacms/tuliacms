<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Framework\Form\FormType;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\FieldTypeBuilder\FieldTypeBuilderInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\Field;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Query\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class UserTypeaheadTypeBuilder implements FieldTypeBuilderInterface
{
    protected AuthenticatedUserProviderInterface $authenticatedUserProvider;

    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    public function build(Field $field, array $options, ContentType $contentType): array
    {
        /** @var User $author */
        $author = $this->authenticatedUserProvider->getUser();

        return [
            'empty_data' => $author->getId(),
        ];
    }
}
