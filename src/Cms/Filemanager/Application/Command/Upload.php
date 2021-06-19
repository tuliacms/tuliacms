<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Application\Command;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Filemanager\Application\Command\Helper\FileResponseFormatter;
use Tulia\Cms\Filemanager\Ports\Domain\WriteModel\FileTypeEnum;
use Tulia\Cms\Filemanager\Ports\Domain\Command\CommandInterface;
use Tulia\Cms\Filemanager\Ports\Domain\ReadModel\FileFinderInterface;
use Tulia\Cms\Filemanager\Ports\Domain\ReadModel\FileFinderScopeEnum;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Image\ImageManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Upload implements CommandInterface
{
    protected ConnectionInterface $connection;

    protected SluggerInterface $slugger;

    protected UuidGeneratorInterface $uuidGenerator;

    protected FileFinderInterface $finder;

    protected ImageManagerInterface $imageManager;

    protected FileResponseFormatter $formatter;

    protected string $projectDir;

    public function __construct(
        ConnectionInterface $connection,
        SluggerInterface $slugger,
        UuidGeneratorInterface $uuidGenerator,
        FileFinderInterface $finder,
        ImageManagerInterface $imageManager,
        FileResponseFormatter $formatter,
        string $projectDir
    ) {
        $this->connection = $connection;
        $this->slugger = $slugger;
        $this->uuidGenerator = $uuidGenerator;
        $this->finder = $finder;
        $this->imageManager = $imageManager;
        $this->formatter = $formatter;
        $this->projectDir = $projectDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'upload';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request): array
    {
        $directory = $this->connection->fetchAllAssociative('SELECT * FROM #__filemanager_directory WHERE id = :id', [
            'id' => $request->get('directory', DirectoryTree::ROOT),
        ]);

        if (empty($directory)) {
            $directory = [[
                'id' => DirectoryTree::ROOT,
            ]];
        }

        $files = [];

        foreach ($request->files as $source) {
            $id = $this->upload($source, $directory[0]);

            $file = $this->finder->findOne(['id' => $id], FileFinderScopeEnum::FILEMANAGER);

            $files[] = $this->formatter->format($file);
        }

        return [
            'status' => 'success',
            'uploaded_files' => $files,
        ];
    }

    /**
     * @param File $file
     * @param array $directory
     */
    private function upload(File $file, array $directory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->guessExtension() ?? pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));

        $path = 'uploads/' . date('Y/m');
        $destination = $this->projectDir . '/public/' . $path;

        $safeFilename = $this->slugger->filename($originalFilename);
        $safeFilename = $this->uniqueName($destination, $safeFilename, $extension);
        $newFilename = $safeFilename . '.' . $extension;

        if (is_dir($destination) === false) {
            if (! mkdir($destination, 0777, true) && !is_dir($destination)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $destination));
            }
        }

        try {
            $file->move($destination, $newFilename);

            return $this->save([
                'directory' => $directory['id'],
                'filename'  => $newFilename,
                'extension' => $extension,
                'type'      => $this->guessType($extension),
                'mimetype'  => '',//$file->getMimeType(),
                'size'      => $file->getSize(),
                'path'      => $path,
            ]);
        } catch (FileException $e) {
            dump($e);
            exit;
            // ... handle exception if something happens during file upload
        }
    }

    private function save(array $file): string
    {
        $file['id'] = $this->uuidGenerator->generate();
        $file['created_at'] = date('Y-m-d H:i:s');

        $this->connection->insert('#__filemanager_file', $file);

        if ($file['type'] === FileTypeEnum::IMAGE) {
            $this->createThumbnails($file);
        }

        return $file['id'];
    }

    private function uniqueName(string $directory, string $filename, string $extension): string
    {
        if (is_file($directory . '/' . $filename . '.' . $extension) === false) {
            return $filename;
        }

        $iteration = 1;

        do {
            $newFilename = $filename . '-' . $iteration;

            if (is_file($directory . '/' . $newFilename . '.' . $extension) === false) {
                return $newFilename;
            }

            $iteration++;
        } while(true);
    }

    private function guessType(string $extension): string
    {
        switch ($extension) {
            case 'png':
            case 'jpg':
            case 'jpeg': return FileTypeEnum::IMAGE;
            case 'zip':
            case 'rar':
            case 'gz':
            case 'tar': return FileTypeEnum::ARCHIVE;
            case 'txt':
            case 'doc':
            case 'docx': return FileTypeEnum::DOCUMENT;
            case 'pdf': return FileTypeEnum::PDF;
            case 'svg': return FileTypeEnum::SVG;
        }

        return FileTypeEnum::FILE;
    }

    private function createThumbnails(array $file): void
    {
        $basepath = $this->projectDir . '/public/' . $file['path'];

        $safeFilename = $this->uniqueName(
            $basepath,
            pathinfo($file['filename'], PATHINFO_FILENAME) . '-thumbnail',
            $file['extension'],
        );
        $newFilename = $safeFilename . '.' . $file['extension'];

        $this->createThumbnail(
            $basepath . '/' . $file['filename'],
            $basepath . '/' . $newFilename,
            200,
            200
        );
    }

    private function createThumbnail(string $source, string $destination, int $width, int $height): void
    {
        $img = $this->imageManager->make($source);
        $img->fit($width, $height, function ($constraint) {
            $constraint->upsize();
        });
        $img->save($destination);
    }
}
