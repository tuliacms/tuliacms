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
        /** @var User $author */
        $author = $this->authenticatedUserProvider->getUser();

        $nodeType->addField(new Field([
            'name' => 'author_id',
            'type' => 'user',
            'label' => '',
            'constraints' => [
                ['name' => 'required'],
            ],
            'options' => [
                'empty_data' => $author->getId(),
            ]
        ]));
    }
}
