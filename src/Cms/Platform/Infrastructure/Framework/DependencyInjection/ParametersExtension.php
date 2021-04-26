<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\DependencyInjection;

use Tulia\Component\DependencyInjection\ContainerInterface;
use Tulia\Component\DependencyInjection\Extension\AbstractExtension;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ParametersExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerInterface $container): void
    {
        if (tulia_installed() === false) {
            return;
        }

        /** @var ConnectionInterface $connection */
        $connection = $container->get(ConnectionInterface::class);
        $settings   = $connection->fetchAll('SELECT * FROM #__parameter');

        foreach ($settings as $setting) {
            $container->setParameter($setting['name'], $this->decode($setting['value'], $setting['type']));
        }
    }

    /**
     * @param string|null $value
     * @param string $type
     *
     * @return mixed
     */
    private function decode(?string $value, string $type)
    {
        switch ($type) {
            case 'array': return json_decode($value, true);
            default: return $value;
        }
    }
}
