<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType\Service\Decorator;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeDecoratorInterface;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Query\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class AuthorDecorator implements ContentTypeDecoratorInterface
{
    protected AuthenticatedUserProviderInterface $authenticatedUserProvider;

    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    public function decorate(ContentType $contentType): void
    {
        if ($contentType->isType('node') === false) {
            return;
        }

        $contentType->addField(new Field([
            'code' => 'author_id',
            'type' => 'user',
            'name' => 'author',
            'is_internal' => true,
            'builder_options' => function () {
                /** @var User $author */
                $author = $this->authenticatedUserProvider->getUser();

                return [
                    'empty_data' => $author->getId(),
                    'constraints' => [
                        'required' => [],
                    ],
                ];
            }
        ]));
    }
}
