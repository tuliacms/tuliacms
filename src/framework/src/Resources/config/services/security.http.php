<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Framework\Security\Http\Authentication\AuthenticationUtils;
use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Security\Http\Headers\ResponseHeadersFixer;

$builder->setDefinition(ResponseHeadersFixer::class, ResponseHeadersFixer::class, [
    'tags' => [
        tag_event_listener(ResponseEvent::class, 0, 'removeHeaders'),
    ],
]);

$builder->setDefinition(AuthenticationUtils::class, AuthenticationUtils::class, [
    'arguments' => [
        service(RequestStack::class),
    ],
]);
