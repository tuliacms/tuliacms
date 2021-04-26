<?php declare(strict_types=1);

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Cms\Platform\Shared\Breadcrumbs\Breadcrumbs;
use Tulia\Cms\Platform\Shared\Breadcrumbs\BreadcrumbsInterface;
use Tulia\Cms\Platform\Shared\Document\Document;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Tulia\Cms\Platform\Shared\Slug\SimpleSlugGenerator;
use Tulia\Cms\Platform\Shared\Slug\SluggerInterface;
use Tulia\Cms\Platform\Shared\Slug\SymfonyStringGenerator;
use Tulia\Cms\Platform\Shared\Uuid\RamseyUuidGenerator;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;

/** @var ContainerBuilderInterface $builder */

/*$builder->setDefinition(UuidGeneratorInterface::class, RamseyUuidGenerator::class);*/
$builder->setDefinition(BreadcrumbsInterface::class, Breadcrumbs::class);
$builder->setDefinition(DocumentInterface::class, Document::class);
$builder->setDefinition(SymfonyStringGenerator::class, SymfonyStringGenerator::class);
$builder->setDefinition(SimpleSlugGenerator::class, SimpleSlugGenerator::class, [
    'arguments' => [
        service(RequestStack::class),
    ],
]);

$builder->setAlias(SluggerInterface::class, SimpleSlugGenerator::class);
