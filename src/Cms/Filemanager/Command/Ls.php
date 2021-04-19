<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Command;

use Tulia\Cms\Filemanager\Command\Helper\FileResponseFormatter;
use Tulia\Cms\Filemanager\Enum\ScopeEnum;
use Tulia\Cms\Filemanager\File;
use Tulia\Cms\Filemanager\Query\FinderFactoryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Ls implements CommandInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var FileResponseFormatter
     */
    protected $fileResponseFormatter;

    /**
     * @param ConnectionInterface $connection
     * @param FinderFactoryInterface $finderFactory
     * @param FileResponseFormatter $fileResponseFormatter
     */
    public function __construct(
        ConnectionInterface $connection,
        FinderFactoryInterface $finderFactory,
        FileResponseFormatter $fileResponseFormatter
    ) {
        $this->connection    = $connection;
        $this->finderFactory = $finderFactory;
        $this->fileResponseFormatter = $fileResponseFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'ls';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): array
    {
        $directory = $request->get('directory', DirectoryTree::ROOT);
        $filter    = $request->get('filter', []);

        switch ($request->get('orderBy', 'created_at')) {
            case 'filename': $orderBy = 'filename'; break;
            default: $orderBy = 'created_at';
        }

        $criteria = [
            'directory' => $directory,
            'order_by'  => $orderBy,
            'order_dir' => $request->get('orderDir', 'desc') === 'desc' ? 'DESC' : 'ASC',
        ];

        if (isset($filter['type']) && $filter['type'] !== '*') {
            $types = $filter['type'];

            if (\is_array($types) === false) {
                $types = [$types];
            }

            $criteria['type'] = $types;
        }

        $finder = $this->finderFactory->getInstance(ScopeEnum::FILEMANAGER);
        $finder->setCriteria($criteria);
        $finder->fetch();

        $directories = $this->connection->fetchAll("SELECT * FROM #__filemanager_directory WHERE parent_id = :parent_id ORDER BY `name` ASC", [
            'parent_id' => $directory
        ]);

        $result = [];

        foreach ($directories as $directory) {
            $result[] = [
                'type' => 'directory',
                'id'   => $directory['id'],
                'name' => $directory['name'],
                'preview' => null,
                'size' => 0,
                'size_formatted' => '0b',
            ];
        }

        /** @var File $file */
        foreach ($finder->getResult() as $file) {
            $result[] = $this->fileResponseFormatter->format($file);
        }

        return $result;
    }
}
