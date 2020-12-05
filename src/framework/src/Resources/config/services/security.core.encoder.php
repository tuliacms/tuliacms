<?php declare(strict_types=1);

use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;
use Symfony\Component\Security\Core\User\User;
use Tulia\Component\DependencyInjection\ContainerBuilderInterface;

/** @var ContainerBuilderInterface $builder */

$builder->setDefinition(EncoderFactoryInterface::class, EncoderFactory::class, [
    'arguments' => [
        [
            User::class => [
                'algorithm' => 'auto',
                'class' => NativePasswordEncoder::class,
                'arguments' => [],
                'hash_algorithm' => 'native',
            ],
        ],
    ],
]);
