<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Filemanager\Application\Service\Cropper;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderInterface;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\FileFinderScopeEnum;
use Tulia\Cms\Filemanager\Domain\ReadModel\Model\File;
use Tulia\Cms\Filemanager\Domain\WriteModel\FileTypeEnum;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;

/**
 * @author Adam Banaszkiewicz
 */
class Image extends AbstractController
{
    protected FileFinderInterface $finder;

    protected Cropper $cropper;

    public function __construct(FileFinderInterface $finder, Cropper $cropper)
    {
        $this->finder = $finder;
        $this->cropper = $cropper;
    }

    public function size(Request $request, string $size, string $id): RedirectResponse
    {
        $image = $this->getImage($id);

        if ($size === 'original') {
            $path = '/' . $image->getPath() . '/' . $image->getFilename();
        } else {
            $path = $this->cropper->crop($image, $size);
        }

        return new RedirectResponse(
            $request->getUriForPath($path),
            Response::HTTP_MOVED_PERMANENTLY
        );
    }

    private function getImage(string $id): File
    {
        $image = $this->finder->findOne([
            'id' => $id,
            'type' => FileTypeEnum::IMAGE
        ], FileFinderScopeEnum::SINGLE);

        if (! $image) {
            throw $this->createNotFoundException('Image not found.');
        }

        return $image;
    }
}
