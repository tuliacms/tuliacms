<?php declare(strict_types=1);

/** @var ContainerBuilderInterface $builder */

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Options\Application\Service\Options;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\User\Application\Command\UserStorage;
use Tulia\Cms\User\Application\Event\UserPreCreateEvent;
use Tulia\Cms\User\Application\Event\UserPreDeleteEvent;
use Tulia\Cms\User\Application\Event\UserPreUpdateEvent;
use Tulia\Cms\User\Application\EventListener\MetadataLoader;
use Tulia\Cms\User\Application\EventListener\PasswordEncoder;
use Tulia\Cms\User\Application\EventListener\SelfUserDeleteDetector;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProvider;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Cms\User\Application\Service\Avatar\Uploader;
use Tulia\Cms\User\Application\Service\Avatar\UploaderInterface;
use Tulia\Cms\User\Domain\RepositoryInterface;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\DefaultMetadataRegistrator;
use Tulia\Cms\User\Infrastructure\Cms\Metadata\Loader;
use Tulia\Cms\User\Infrastructure\Cms\Settings\SettingsGroup;
use Tulia\Cms\User\Infrastructure\Framework\Form\FormType\UserTypeaheadType;
use Tulia\Cms\User\Infrastructure\Framework\Translator\UserLocaleResolver;
use Tulia\Cms\User\Infrastructure\Framework\Twig\Extension\UserExtension;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\EmailUniqueValidator;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\PasswordValidator as PasswordValidatorConstraint;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\UsernameUniqueValidator;
use Tulia\Cms\User\Infrastructure\Framework\Validator\Constraints\UsernameValidator as UsernameValidatorConstraint;
use Tulia\Cms\User\Infrastructure\Framework\Validator\PasswordValidator;
use Tulia\Cms\User\Infrastructure\Framework\Validator\PasswordValidatorFactory;
use Tulia\Cms\User\Infrastructure\Framework\Validator\PasswordValidatorInterface;
use Tulia\Cms\User\Infrastructure\Framework\Validator\UsernameValidator;
use Tulia\Cms\User\Infrastructure\Framework\Validator\UsernameValidatorFactory;
use Tulia\Cms\User\Infrastructure\Framework\Validator\UsernameValidatorInterface;
use Tulia\Cms\User\Infrastructure\Persistence\Domain\DbalPersister;
use Tulia\Cms\User\Infrastructure\Persistence\Domain\DbalRepository;
use Tulia\Cms\User\Infrastructure\Persistence\Query\DatatableFinder;
use Tulia\Cms\User\Query\Event\QueryFilterEvent;
use Tulia\Cms\User\Query\Factory\UserFactory;
use Tulia\Cms\User\Query\Factory\UserFactoryInterface;
use Tulia\Cms\User\Query\FinderFactory;
use Tulia\Cms\User\Query\FinderFactoryInterface;
use Tulia\Cms\User\UI\Web\Form\Extension\AvatarExtension;
use Tulia\Cms\User\UI\Web\Form\Extension\SecurityExtension;
use Tulia\Cms\User\UI\Web\Form\MyAccount\Extension\BasicsExtension as BackendMyAccountBasicsExtension;
use Tulia\Cms\User\UI\Web\Form\MyAccount\MyAccountForm;
use Tulia\Cms\User\UI\Web\Form\MyAccount\MyAccountFormManagerFactory;
use Tulia\Cms\User\UI\Web\Form\ScopeEnum;
use Tulia\Cms\User\UI\Web\Form\UserForm\Extension\BasicsExtension as BackendUserBasicsExtension;
use Tulia\Cms\User\UI\Web\Form\UserForm\UserForm;
use Tulia\Cms\User\UI\Web\Form\UserForm\UserFormManagerFactory;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;
use Tulia\Component\FormBuilder\Manager\ManagerFactoryInterface;
use Tulia\Component\Routing\RouterInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Shared\Uuid\UuidGeneratorInterface;
use Tulia\Framework\Database\ConnectionInterface;
use Tulia\Framework\Kernel\Event\RequestEvent;

$builder->setDefinition(FinderFactoryInterface::class, FinderFactory::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(EventDispatcherInterface::class),
    ],
]);

$builder->setDefinition(UserExtension::class, UserExtension::class, [
    'arguments' => [
        service(AuthenticatedUserProviderInterface::class),
    ],
    'tags' => [ tag('twig.extension') ],
]);

$builder->setDefinition(AuthenticatedUserProviderInterface::class, AuthenticatedUserProvider::class, [
    'arguments' => [
        service(TokenStorageInterface::class),
        service(FinderFactoryInterface::class),
    ],
]);

/*$builder->setDefinition(PasswordValidatorInterface::class, PasswordValidator::class, [
    'factory' => [PasswordValidatorFactory::class, 'factory'],
    'arguments' => [
        service(Options::class),
    ],
]);*/

/*$builder->setDefinition(UsernameValidatorInterface::class, UsernameValidator::class, [
    'factory' => [UsernameValidatorFactory::class, 'factory'],
    'arguments' => [
        service(Options::class),
    ],
]);*/

$builder->setDefinition(DbalPersister::class, DbalPersister::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
    ],
]);

$builder->setDefinition(RepositoryInterface::class, DbalRepository::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(DbalPersister::class),
        service(HydratorInterface::class),
        service(SyncerInterface::class),
    ],
]);

$builder->setDefinition(UserStorage::class, UserStorage::class, [
    'arguments' => [
        service(RepositoryInterface::class),
        service(EventBusInterface::class),
    ],
]);

$builder->setDefinition(UserFormManagerFactory::class, UserFormManagerFactory::class, [
    'arguments' => [
        service(ManagerFactoryInterface::class),
        service(FormFactoryInterface::class),
        service(UserStorage::class),
    ],
]);

$builder->setDefinition(MyAccountFormManagerFactory::class, MyAccountFormManagerFactory::class, [
    'arguments' => [
        service(ManagerFactoryInterface::class),
        service(FormFactoryInterface::class),
        service(UserStorage::class),
    ],
]);

/*$builder->setDefinition(DefaultMetadataRegistrator::class, DefaultMetadataRegistrator::class, [
    'tags' => [ tag('metadata.registrator') ],
]);*/

$builder->setDefinition(PasswordValidatorConstraint::class, PasswordValidatorConstraint::class, [
    'arguments' => [
        service(PasswordValidatorInterface::class),
    ],
]);

$builder->setDefinition(UsernameValidatorConstraint::class, UsernameValidatorConstraint::class, [
    'arguments' => [
        service(UsernameValidatorInterface::class),
    ],
]);

$builder->setDefinition(UsernameUniqueValidator::class, UsernameUniqueValidator::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
    ],
]);

$builder->setDefinition(EmailUniqueValidator::class, EmailUniqueValidator::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
    ],
]);

$builder->setDefinition(UserFactoryInterface::class, UserFactory::class, [
    'arguments' => [
        service(UuidGeneratorInterface::class),
        service(Loader::class),
    ],
]);

/*$builder->setDefinition(SettingsGroup::class, SettingsGroup::class, [
    'tags' => [ tag('settings.group') ],
]);*/

$builder->setDefinition(UploaderInterface::class, Uploader::class, [
    'arguments' => [
        parameter('kernel.public_dir'),
    ],
]);

$builder->setDefinition(UserLocaleResolver::class, UserLocaleResolver::class, [
    'arguments' => [
        service(AuthenticatedUserProviderInterface::class),
    ],
    'tags' => [
        tag_event_listener(RequestEvent::class, 500),
    ],
]);

$builder->setDefinition(SelfUserDeleteDetector::class, SelfUserDeleteDetector::class, [
    'arguments' => [
        service(AuthenticatedUserProviderInterface::class),
    ],
    'tags' => [
        tag_event_listener(UserPreDeleteEvent::class, 1000),
    ],
]);

$builder->setDefinition(PasswordEncoder::class, PasswordEncoder::class, [
    'arguments' => [
        service(EncoderFactoryInterface::class),
    ],
    'tags' => [
        tag_event_listener(UserPreUpdateEvent::class, 1000),
        tag_event_listener(UserPreCreateEvent::class, 1000),
    ],
]);

$builder->setDefinition(Loader::class, Loader::class, [
    'arguments' => [
        service(SyncerInterface::class),
    ],
]);

$builder->setDefinition(MetadataLoader::class, MetadataLoader::class, [
    'arguments' => [
        service(Loader::class),
    ],
    'tags' => [
        tag_event_listener(QueryFilterEvent::class),
    ],
]);

$builder->setDefinition(UserForm::class, UserForm::class, [
    'arguments' => [
        service(TranslatorInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(MyAccountForm::class, MyAccountForm::class, [
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(SecurityExtension::class, SecurityExtension::class, [
    'arguments' => [
        [
            ScopeEnum::BACKEND_USER,
        ],
    ],
    'tags' => [ tag('form_extension') ],
]);

$builder->setDefinition(AvatarExtension::class, AvatarExtension::class, [
    'arguments' => [
        service(UploaderInterface::class),
        [
            ScopeEnum::BACKEND_MY_ACCOUNT,
            ScopeEnum::BACKEND_USER,
        ],
    ],
    'tags' => [ tag('form_extension') ],
]);

$builder->setDefinition(BackendUserBasicsExtension::class, BackendUserBasicsExtension::class, [
    'arguments' => [
        service(TranslatorInterface::class),
        service(CurrentWebsiteInterface::class),
        [
            ScopeEnum::BACKEND_USER,
        ],
    ],
    'tags' => [ tag('form_extension') ],
]);

$builder->setDefinition(BackendMyAccountBasicsExtension::class, BackendMyAccountBasicsExtension::class, [
    'arguments' => [
        service(TranslatorInterface::class),
        service(CurrentWebsiteInterface::class),
        [
            ScopeEnum::BACKEND_MY_ACCOUNT,
        ],
    ],
    'tags' => [ tag('form_extension') ],
]);


$builder->setDefinition(UserTypeaheadType::class, UserTypeaheadType::class, [
    'arguments' => [
        service(FinderFactoryInterface::class),
    ],
    'tags' => [ tag('form.type') ],
]);

$builder->setDefinition(DatatableFinder::class, DatatableFinder::class, [
    'arguments' => [
        service(ConnectionInterface::class),
        service(CurrentWebsiteInterface::class),
        service(RouterInterface::class),
        service(TranslatorInterface::class),
    ],
]);


$builder->mergeParameter('translation.directory_list', [
    dirname(__DIR__) . '/translations',
]);

$builder->mergeParameter('templating.paths', [
    'cms/user' => dirname(__DIR__) . '/views/frontend',
    'backend/user' => dirname(__DIR__) . '/views/backend',
]);
