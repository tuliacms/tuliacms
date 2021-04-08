<?php

declare(strict_types=1);

namespace Tulia\Framework\Migrations\Configuration;

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\Configuration\Migration\ConfigurationFile;

/**
 * @author Adam Banaszkiewicz
 */
class RuntimeConfiguration extends ConfigurationFile
{
    /**
     * @var iterable|array
     */
    protected $modules;

    /**
     * @var array
     */
    protected $paths;

    /**
     * @param string $projectDir
     * @param array $paths
     * @param iterable|array $modules
     */
    public function __construct(string $projectDir, array $paths, iterable $modules)
    {
        parent::__construct($projectDir);

        $this->paths   = $paths;
        $this->modules = $modules;
    }

    public function getConfiguration() : Configuration
    {
        $config = [
            'table_storage' => [
                'table_name' => '#__migration_versions',
                'version_column_name' => 'version',
                'version_column_length' => 1024,
                'executed_at_column_name' => 'executed_at',
                'execution_time_column_name' => 'execution_time',
            ],
            'migrations_paths' => $this->paths,
            'all_or_nothing' => true,
            'check_database_platform' => true,
        ];

        if (isset($config['migrations_paths'])) {
            $config['migrations_paths'] = $this->getDirectoriesRelativeToFile(
                $config['migrations_paths'],
                $this->file
            );
        }

        return (new ConfigurationArray($config))->getConfiguration();
    }
}
