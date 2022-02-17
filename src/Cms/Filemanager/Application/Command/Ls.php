<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Command;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Filemanager\Application\Command\Helper\FileResponseFormatter;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderScopeEnum;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Ls implements CommandInterface
{
    protected ConnectionInterface $connection;

    protected FileFinderInterface $finder;

    protected FileResponseFormatter $fileResponseFormatter;

    public function __construct(
        ConnectionInterface $connection,
        FileFinderInterface $finder,
        FileResponseFormatter $fileResponseFormatter
    ) {
        $this->connection = $connection;
        $this->finder = $finder;
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

        $files = $this->finder->find($criteria, FileFinderScopeEnum::FILEMANAGER);

        $directories = $this->connection->fetchAllAssociative("SELECT * FROM #__filemanager_directory WHERE parent_id = :parent_id ORDER BY `name` ASC", [
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
        foreach ($files as $file) {
            $result[] = $this->fileResponseFormatter->format($file);
        }

        return $result;
    }
}
