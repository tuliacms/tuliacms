<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Filemanager\Application\Service\ImageUrlResolver;
use Tulia\Cms\Filemanager\Ports\Domain\ReadModel\FileFinderInterface;
use Tulia\Cms\Filemanager\Ports\Domain\ReadModel\FileFinderScopeEnum;
use Tulia\Cms\Filemanager\Ports\Domain\WriteModel\FileTypeEnum;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\Model\File;
use Tulia\Cms\Filemanager\Domain\Generator\Html;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class FilemanagerExtension extends AbstractExtension
{
    protected FileFinderInterface $finder;

    protected ImageUrlResolver $urlResolver;

    protected string $publicDir;

    public function __construct(FileFinderInterface $finder, ImageUrlResolver $urlResolver, string $publicDir)
    {
        $this->finder = $finder;
        $this->urlResolver = $urlResolver;
        $this->publicDir = $publicDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('image',        [$this, 'image'],    ['is_safe' => [ 'html' ]]),
            new TwigFunction('image_url',    [$this, 'imageUrl'], ['is_safe' => [ 'html' ]]),
            new TwigFunction('svg',          [$this, 'svg'],      ['is_safe' => [ 'html' ]]),
            new TwigFunction('svg_url',      [$this, 'svgUrl'],   ['is_safe' => [ 'html' ]]),
            new TwigFunction('gallery',      [$this, 'gallery'],  ['is_safe' => [ 'html' ]]),
            new TwigFunction('is_file_type', [$this, 'isFileType'],  ['is_safe' => [ 'html' ]]),
        ];
    }

    /**
     * Params:
     *     - attributes = List of img tag attributes for HTML
     *     - size = Size name of the image.
     *     - version = Version of the image, added at the end of the URL
     *       as query string, to force reload by browser.
     */
    public function image(string $id, array $params = []): string
    {
        $image = $this->finder->findOne([
            'id' => $id,
            'type' => FileTypeEnum::IMAGE,
        ], FileFinderScopeEnum::SINGLE);

        if ($image === null) {
            return '';
        }

        $data = array_merge([
            'alt' => '',
        ], $params['attributes'] ?? []);

        $data['src'] = $this->urlResolver->size(
            $image,
            isset($params['size']) && empty($params['size']) === false
                ? $params['size']
                : 'original'
        );

        if (isset($params['version']) && empty($params['version']) === false) {
            $data['src'] .= '?version=' . $params['version'];
        }

        return (new Html())->generateImageTag($data);
    }

    /**
     * @param string|File $id
     */
    public function imageUrl($id, string $sizeName): string
    {
        if ($id instanceof File) {
            $image = $id;
        } else {
            $image = $this->finder->findOne([
                'id' => $id,
                'type' => FileTypeEnum::IMAGE,
            ], FileFinderScopeEnum::SINGLE);

            if ($image === null) {
                return '';
            }
        }

        return $this->urlResolver->size($image, $sizeName);
    }

    public function gallery(array $ids, array $params = []): string
    {
        $images = $this->finder->find([
            'id__in' => $ids,
            'type'   => FileTypeEnum::IMAGE,
            'order_by'  => 'id',
            'order_dir' => $ids
        ], FileFinderScopeEnum::SINGLE);

        if ($images->count() === 0) {
            return '';
        }

        $generator = new Html();

        $result = '<div class="tulia-gallery tulia-gallery-type-image">';

        foreach ($images as $image) {
            $result .= '<div class="tulia-gallery-item">' . $generator->generateImageTag([
                'src' => '/' . $image->getPath() . '/' . $image->getFilename()
            ]) . '</div>';
        }

        return $result . '</div>';
    }

    public function svg(string $id, array $params = []): string
    {
        $svg = $this->finder->findOne([
            'id' => $id,
            'type' => FileTypeEnum::SVG,
        ], FileFinderScopeEnum::SINGLE);

        if ($svg === null) {
            return '';
        }

        $data = array_merge([
            'alt' => '',
        ], $params['attributes'] ?? []);

        $data['src'] = '/' . $svg->getPath() . '/' . $svg->getFilename();

        if (isset($params['version']) && empty($params['version']) === false) {
            $data['src'] .= '?version=' . $params['version'];
        }

        return (new Html())->generateImageTag($data);
    }

    public function svgUrl($id): string
    {
        if ($id instanceof File) {
            $svg = $id;
        } else {
            $svg = $this->finder->findOne([
                'id' => $id,
                'type' => FileTypeEnum::SVG,
            ], FileFinderScopeEnum::SINGLE);

            if ($svg === null) {
                return '';
            }
        }

        return '/' . $svg->getPath() . '/' . $svg->getFilename();
    }

    public function isFileType(string $id, string $type): bool
    {
        return (bool) $this->finder->findOne([
            'id' => $id,
            'type' => $type,
        ], FileFinderScopeEnum::SINGLE);
    }
}
