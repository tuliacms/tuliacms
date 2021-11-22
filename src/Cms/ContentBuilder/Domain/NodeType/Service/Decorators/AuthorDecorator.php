<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\NodeType\Service\Decorators;

use Tulia\Cms\ContentBuilder\Domain\Field\Model\Field;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Model\NodeType;
use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeDecoratorInterface;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Query\Model\User;

/**
 * @author Adam Banaszkiewicz
 */
class AuthorDecorator implements NodeTypeDecoratorInterface
{
    protected AuthenticatedUserProviderInterface $authenticatedUserProvider;

    public function __construct(AuthenticatedUserProviderInterface $authenticatedUserProvider)
    {
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    public function decorate(NodeType $nodeType): void
    {
        $nodeType->addField(new Field([
            'name' => 'author_id',
            'type' => 'user',
            'label' => 'author',
            'internal' => true,
            'builder_options' => function () {
                /** @var User $author */
                $author = $this->authenticatedUserProvider->getUser();

                return [
                    'empty_data' => $author->getId(),
                    'constraints' => [
                        ['name' => 'required'],
                    ],
                ];
            }
        ]));
    }
}
