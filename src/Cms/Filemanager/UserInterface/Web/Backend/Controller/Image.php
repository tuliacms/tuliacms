<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\Filemanager\Application\Service\Cropper;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\Enum\FilemanagerFinderScopeEnum;
use Tulia\Cms\Filemanager\Enum\TypeEnum;
use Tulia\Cms\Filemanager\Query\FinderFactoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Image extends AbstractController
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var Cropper
     */
    protected $cropper;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param Cropper $cropper
     */
    public function __construct(FinderFactoryInterface $finderFactory, Cropper $cropper)
    {
        $this->finderFactory = $finderFactory;
        $this->cropper = $cropper;
    }

    public function size(Request $request, $size, $id)
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

    private function getImage(string $id)
    {
        $image = $this->finderFactory->getInstance(FilemanagerFinderScopeEnum::SINGLE)->find($id, TypeEnum::IMAGE);

        if (!$image) {
            throw $this->createNotFoundException('Image not found.');
        }

        return $image;
    }
}
