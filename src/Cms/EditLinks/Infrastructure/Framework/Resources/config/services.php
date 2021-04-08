<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\EditLinks\Infrastructure\Cms\FrontendToolbar\LinksProvider;
use Tulia\Cms\EditLinks\Infrastructure\Framework\Twig\Extension\EditLinksExtension;
use Tulia\Cms\FrontendToolbar\Application\Helper\HelperInterface;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(EditLinksExtension::class, EditLinksExtension::class, [
        'arguments' => [
            service(TranslatorInterface::class),
            service(EventDispatcherInterface::class),
            service(AuthorizationCheckerInterface::class),
            service(DetectorInterface::class),
        ],
        'tags' => [tag('twig.extension')],
    ],
);

$builder->setDefinition(LinksProvider::class, LinksProvider::class, [
    'arguments' => [
        service(HelperInterface::class),
    ],
    'tags' => [ tag('frontend_toolbar.links.provider') ],
]);

$builder->mergeParameter('templating.paths', [
    'cms/edit_links' => dirname(__DIR__) . '/views/frontend',
]);
