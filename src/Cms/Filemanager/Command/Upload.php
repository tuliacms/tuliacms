<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Command;

use Tulia\Component\Image\ImageManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Tulia\Cms\Filemanager\Command\Helper\FileResponseFormatter;
use Tulia\Cms\Filemanager\Enum\ScopeEnum;
use Tulia\Cms\Filemanager\Enum\TypeEnum;
use Tulia\Cms\Filemanager\Query\FinderFactoryInterface;
use Tulia\Cms\Platform\Shared\Slug\SluggerInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Upload implements CommandInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var SluggerInterface
     */
    protected $slugger;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var ImageManagerInterface
     */
    protected $imageManager;

    /**
     * @var FileResponseFormatter
     */
    protected $formatter;

    /**
     * @var string
     */
    protected $projectDir;

    /**
     * @param ConnectionInterface $connection
     * @param SluggerInterface $slugger
     * @param UuidGeneratorInterface $uuidGenerator
     * @param FinderFactoryInterface $finderFactory
     * @param ImageManagerInterface $imageManager
     * @param FileResponseFormatter $formatter
     * @param string $projectDir
     */
    public function __construct(
        ConnectionInterface $connection,
        SluggerInterface $slugger,
        UuidGeneratorInterface $uuidGenerator,
        FinderFactoryInterface $finderFactory,
        ImageManagerInterface $imageManager,
        FileResponseFormatter $formatter,
        string $projectDir
    ) {
        $this->connection    = $connection;
        $this->slugger       = $slugger;
        $this->uuidGenerator = $uuidGenerator;
        $this->finderFactory = $finderFactory;
        $this->imageManager  = $imageManager;
        $this->formatter     = $formatter;
        $this->projectDir    = $projectDir;
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
        $directory = $source = $this->connection->fetchAll('SELECT * FROM #__filemanager_directory WHERE id = :id', [
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

            $file = $this->finderFactory->getInstance(ScopeEnum::FILEMANAGER)->find($id);

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
            if (!mkdir($destination, 0777, true) && !is_dir($destination)) {
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

        if ($file['type'] === TypeEnum::IMAGE) {
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
            case 'jpeg': return TypeEnum::IMAGE;
            case 'zip':
            case 'rar':
            case 'gz':
            case 'tar': return TypeEnum::ARCHIVE;
            case 'txt':
            case 'doc':
            case 'docx': return TypeEnum::DOCUMENT;
            case 'pdf': return TypeEnum::PDF;
            case 'svg': return TypeEnum::SVG;
        }

        return TypeEnum::FILE;
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
