<?php declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Website\Infrastructure\Framework\Website\Storage\DatabaseStorage;
use Tulia\Cms\Website\Application\Command\WebsiteStorage;
use Tulia\Cms\Website\Domain\RepositoryInterface;
use Tulia\Cms\Website\Infrastructure\Persistence\Domain\DbalRepository;
use Tulia\Cms\Website\Infrastructure\Persistence\Domain\DbalPersister;
use Tulia\Cms\Website\Query\Factory\WebsiteFactory;
use Tulia\Cms\Website\Query\Factory\WebsiteFactoryInterface;
use Tulia\Cms\Website\Query\FinderFactory;
use Tulia\Cms\Website\Query\FinderFactoryInterface;
use Tulia\Cms\Website\UI\Web\Form\WebsiteFormManagerFactory;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Locale\Storage\StorageInterface;
use Tulia\Component\Routing\Website\Registry;
use Tulia\Component\Routing\Website\RegistryInterface;
use Tulia\Component\Routing\Website\Website;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Framework\Database\ConnectionInterface;
use Tulia\Cms\Website\UI\Web\Form\FormType\LocaleChoiceType;
use Symfony\Contracts\Translation\TranslatorInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(FinderFactoryInterface::class, FinderFactory::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(EventDispatcherInterface::class),
        service(StorageInterface::class),
    ],
]);

$builder->setDefinition(DbalPersister::class, DbalPersister::class, [
    'arguments' => [
        service(ConnectionInterface::class),
    ],
]);

$builder->setDefinition(RepositoryInterface::class, DbalRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(DbalPersister::class),
        service(HydratorInterface::class),
    ],
]);

$builder->setDefinition(WebsiteFactoryInterface::class, WebsiteFactory::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(WebsiteStorage::class, WebsiteStorage::class, [
    'arguments' => [
        service(RepositoryInterface::class),
        service(EventBusInterface::class),
    ],
]);

$builder->setDefinition(WebsiteFormManagerFactory::class, WebsiteFormManagerFactory::class, [
    'arguments' => [
        service(ManagerFactoryInterface::class),
        service(FormFactoryInterface::class),
        service(WebsiteStorage::class),
    ],
]);


$builder->setDefinition(DatabaseStorage::class, DatabaseStorage::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(StorageInterface::class),
    ],
]);

if (tulia_installed()) {
    $builder->setDefinition(RegistryInterface::class, Registry::class, [
        'factory' => function (DatabaseStorage $storage) {
            $websites = new Registry();

            foreach ($storage->all() as $website) {
                $websites->add($website);
            }

            return $websites;
        },
        'arguments' => [
            service(DatabaseStorage::class),
        ],
    ]);
} else {
    $builder->setDefinition(RegistryInterface::class, Registry::class, [
        'factory' => function () {
            $websites = new Registry();

            // Installation locales that are supported with translations
            $localeEn = new Locale('en_US', $_SERVER['HTTP_HOST'], null, null, SslModeEnum::ALLOWED_BOTH, true);
            $localePl = new Locale('pl_PL', $_SERVER['HTTP_HOST'], '/pl', null, SslModeEnum::ALLOWED_BOTH, false);

            $websites->add(new Website(
                'f19b16b2-f52b-442a-aee2-8e0f4fed31b7',
                [$localeEn, $localePl],
                $localeEn,
                '/administrator',
                'Default website'
            ));

            return $websites;
        },
    ]);
}

$builder->setDefinition(LocaleChoiceType::class, LocaleChoiceType::class, [
    'arguments' => [
        service(StorageInterface::class),
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);


$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'backend/website' => dirname(__DIR__) . '/views/backend',
]);
