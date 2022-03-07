<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\UserInterface\CLI\Command;

use DirectoryIterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Tulia\Cms\Filemanager\Domain\ImageSize\ImageSizeRegistryInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FilemanagerThumbnailsClearNonexistentSizesCommand extends Command
{
    protected static $defaultName = 'filemanager:thumbnails:clear-nonexistent-sizes';
    private ImageSizeRegistryInterface $imageSizeRegistry;
    private CurrentWebsiteInterface $currentWebsite;
    private string $publicDirectory;

    public function __construct(
        ImageSizeRegistryInterface $imageSizeRegistry,
        CurrentWebsiteInterface $currentWebsite,
        string $publicDirectory
    ) {
        parent::__construct(static::$defaultName);

        $this->imageSizeRegistry = $imageSizeRegistry;
        $this->currentWebsite = $currentWebsite;
        $this->publicDirectory = $publicDirectory;
    }

    protected function configure()
    {
        $this->addArgument('website', InputArgument::REQUIRED, 'Website for which command should be executed.');
        $this->addOption('dry-run', 'd', InputOption::VALUE_OPTIONAL, 'Only prints info, without remove files', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();

        $basepath = $this->publicDirectory.'/uploads/thumbnails/'.$this->currentWebsite->getId();

        foreach (new DirectoryIterator($basepath) as $dir) {
            if ($dir->isDot()) {
                continue;
            }

            if ($input->getOption('dry-run')) {
                if ($this->imageSizeRegistry->has($dir->getFilename())) {
                    $io->warning(sprintf('Size %s found in files and in System configuration.', $dir->getFilename()));
                } else {
                    $io->info(sprintf('Size %s found in files, but not exists in System configuration. Files should be removed.', $dir->getFilename()));
                }
            } else {
                if ($this->imageSizeRegistry->has($dir->getFilename()) === false) {
                    $fs->remove($dir->getPathname());
                    $io->info(sprintf('Size %s not found in system. All files were removed.', $dir->getFilename()));
                }
            }
        }

        return Command::SUCCESS;
    }
}
