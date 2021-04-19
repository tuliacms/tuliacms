<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Tulia\Cms\Filemanager\Application\Cropper;
use Tulia\Cms\Filemanager\Application\ImageSize\DefaultSizesProvider;
use Tulia\Cms\Filemanager\Application\ImageSize\Registry as ImageSizeRegistry;
use Tulia\Cms\Filemanager\Application\ImageUrlResolver;
use Tulia\Cms\Filemanager\Command\Helper\FileResponseFormatter;
use Tulia\Cms\Filemanager\Command\Ls;
use Tulia\Cms\Filemanager\Command\Upload;
use Tulia\Cms\Filemanager\CommandPropagator;
use Tulia\Cms\Filemanager\CommandPropagatorInterface;
use Tulia\Cms\Filemanager\CommandRegistry;
use Tulia\Cms\Filemanager\CommandRegistryInterface;
use Tulia\Cms\Filemanager\Command\DirectoryTree;
use Tulia\Cms\Filemanager\Infrastructure\Framework\Theme\Customizer\FilepickerControl;
use Tulia\Cms\Filemanager\Query\FinderFactory;
use Tulia\Cms\Filemanager\Query\FinderFactoryInterface;
use Tulia\Cms\Filemanager\Infrastructure\Cms\Shortcode\Gallery;
use Tulia\Cms\Filemanager\Infrastructure\Cms\Shortcode\Image;
use Tulia\Cms\Filemanager\Infrastructure\Framework\Twig\Extension\FilemanagerExtension;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\Image\ImageManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Shared\Slug\SluggerInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(FinderFactoryInterface::class, FinderFactory::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(EventDispatcherInterface::class),
    ],
]);

$builder->setDefinition(CommandPropagatorInterface::class, CommandPropagator::class, [
    'arguments' => [
        service(CommandRegistryInterface::class),
    ],
]);

/*$builder->setDefinition(CommandRegistryInterface::class, CommandRegistry::class, [
    'arguments' => [
        tagged('filemanager.command'),
    ],
]);*/

/*$builder->setDefinition(DirectoryTree::class, DirectoryTree::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
    'tags' => [ tag('filemanager.command') ],
]);*/

/*$builder->setDefinition(Ls::class, Ls::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(FinderFactoryInterface::class),
        service(FileResponseFormatter::class),
    ],
    'tags' => [ tag('filemanager.command') ],
]);*/

$builder->setDefinition(Upload::class, Upload::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(SluggerInterface::class),
        service(UuidGeneratorInterface::class),
        service(FinderFactoryInterface::class),
        service(ImageManagerInterface::class),
        service(FileResponseFormatter::class),
        parameter('kernel.project_dir'),
    ],
    'tags' => [ tag('filemanager.command') ],
]);

/*$builder->setDefinition(Gallery::class, Gallery::class, [
    'tags' => [ tag('shortcode.compiler') ],
]);

$builder->setDefinition(Image::class, Image::class, [
    'tags' => [ tag('shortcode.compiler') ],
]);*/

$builder->setDefinition(ImageUrlResolver::class, ImageUrlResolver::class, [
    'arguments' => [
        service(RouterInterface::class),
    ],
]);

$builder->setDefinition(FilemanagerExtension::class, FilemanagerExtension::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
        service(ImageUrlResolver::class),
        parameter('kernel.public_dir'),
    ],
    'tags' => [ tag('twig.extension') ],
]);

/*$builder->setDefinition(Cropper::class, Cropper::class, [
    'arguments' => [
        service(ImageManagerInterface::class),
        service(ImageSizeRegistry::class),
        parameter('kernel.public_dir'),
    ],
]);

$builder->setDefinition(ImageSizeRegistry::class, ImageSizeRegistry::class, [
    'arguments' => [
        tagged('filemanager.image_size.provider'),
    ],
]);*/

$builder->setDefinition(DefaultSizesProvider::class, DefaultSizesProvider::class, [
    'tags' => [ tag('filemanager.image_size.provider') ],
]);

$builder->setDefinition(FileResponseFormatter::class, FileResponseFormatter::class, [
    'arguments' => [
        service(ImageUrlResolver::class),
    ],
]);

$builder->setDefinition(FilepickerControl::class, FilepickerControl::class, [
    'arguments' => [
        service(EngineInterface::class),
    ],
    'tags' => [ tag('theme.customizer.control') ],
]);



/*$builder->mergeParameter('templating.paths', [
    'backend/filemanager' => dirname(__DIR__, 1) . '/views/backend',
]);*/
