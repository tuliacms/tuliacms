<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Command;

use Tulia\Cms\Platform\Shared\ArraySorter;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class DirectoryTree implements CommandInterface
{
    public const ROOT = '00000000-0000-0000-0000-000000000000';

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'directory-tree';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): array
    {
        $open = $request->get('open', self::ROOT);

        $source = $this->connection->fetchAll('SELECT * FROM #__filemanager_directory ORDER BY `name`');
        $result = [];

        foreach ($source as $dir) {
            $result[] = [
                'id'          => $dir['id'],
                'text'        => $dir['name'],
                'level'       => $dir['level'] + 1,
                'parent_id'   => $dir['parent_id'] ?: self::ROOT,
                'state'       => [
                    'opened'    => false,
                    'selected'  => $open === $dir['id'],
                ],
                'a_attr' => [
                    'data-id' => $dir['id'],
                    'title'   => $dir['name'],
                ],
            ];
        }

        $result[] = [
            'id'          => self::ROOT,
            'text'        => 'Media',
            'level'       => 0,
            'parent_id'   => '',
            'state'       => [
                'opened'    => true,
                'selected'  => $open !== self::ROOT,
            ],
            'a_attr' => [
                'data-id' => self::ROOT,
                'title'   => 'Media',
            ],
        ];

        return (new ArraySorter($result, [ 'flat_result' => false ]))->sort();
    }
}
