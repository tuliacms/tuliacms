<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Assetter;

use Requtize\Assetter\Assetter;
use Requtize\Assetter\AssetterInterface;
use Requtize\Assetter\Collection;

/**
 * @author Adam Banaszkiewicz
 */
class Factory
{
    public static function factory(array $assets, array $config = []): AssetterInterface
    {
        $config = array_merge([
            'global_revision' => null,
            'default_group'   => 'body',
        ], $config);

        $assets = self::prepareAssetsRevisions($assets, $config);

        $collection = new Collection($assets, $config['default_group']);

        return new Assetter($collection);
    }

    protected static function prepareAssetsRevisions(array $source, array $config): array
    {
        $result = [];

        foreach ($source as $name => $value) {
            $revision = [];

            if (isset($value['revision'])) {
                $revision[] = 'asset-'.$value['revision'];
            }

            if ($config['global_revision']) {
                $revision[] = 'tulia-'.$config['global_revision'];
            }

            $value['revision'] = implode('@', $revision);

            $result[$name] = $value;
        }

        return $result;
    }
}
