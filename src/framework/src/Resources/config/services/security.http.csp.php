<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Tulia\Framework\Kernel\Event\ResponseEvent;
use Tulia\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicy;
use Tulia\Framework\Security\Http\ContentSecurityPolicy\ContentSecurityPolicyInterface;
use Tulia\Framework\Security\Http\ContentSecurityPolicy\EventListener\ResponseHeaderRegistrator;
use Tulia\Framework\Twig\Extension\CspExtension;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

$builder->setDefinition(ContentSecurityPolicyInterface::class, ContentSecurityPolicy::class);

$builder->setDefinition(ResponseHeaderRegistrator::class, ResponseHeaderRegistrator::class, [
    'arguments' => [
        service(ContentSecurityPolicyInterface::class),
    ],
    'tags' => [
        tag_event_listener(ResponseEvent::class, -9999, 'appendHeadersToResponse'),
    ],
]);

$builder->setDefinition(CspExtension::class, CspExtension::class, [
    'arguments' => [
        service(ContentSecurityPolicyInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);
