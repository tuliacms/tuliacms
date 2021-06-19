<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Filemanager\Application\Service\ImageUrlResolver;
use Tulia\Cms\Filemanager\Ports\Domain\ReadModel\FileFinderScopeEnum;
use Tulia\Cms\Filemanager\Enum\TypeEnum;
use Tulia\Cms\Filemanager\Domain\ReadModel\Finder\Model\File;
use Tulia\Cms\Filemanager\Generator\Html;
use Tulia\Cms\Filemanager\Query\FinderFactoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class FilemanagerExtension extends AbstractExtension
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var ImageUrlResolver
     */
    protected $urlResolver;

    /**
     * @var string
     */
    protected $publicDir;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param ImageUrlResolver $urlResolver
     */
    public function __construct(FinderFactoryInterface $finderFactory, ImageUrlResolver $urlResolver, string $publicDir)
    {
        $this->finderFactory = $finderFactory;
        $this->urlResolver   = $urlResolver;
        $this->publicDir     = $publicDir;
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
     *
     * @param string $id
     * @param array $params
     *
     * @return string
     */
    public function image(string $id, $params = []): string
    {
        $image = $this->finderFactory->getInstance(FileFinderScopeEnum::SINGLE)->find($id, TypeEnum::IMAGE);

        if ($image === null) {
            return '';
        }

        $data = array_merge([
            'alt' => '',
        ], $params['attributes'] ?? []);
        $data['src'] = $this->urlResolver->size($image, $params['size'] ?? 'original');

        if (isset($params['version']) && empty($params['version']) === false) {
            $data['src'] .= '?version=' . $params['version'];
        }

        return (new Html())->generateImageTag($data);
    }

    /**
     * @param string|File $id
     * @param string $sizeName
     *
     * @return string
     */
    public function imageUrl($id, string $sizeName): string
    {
        if ($id instanceof File) {
            $image = $id;
        } else {
            $image = $this->finderFactory->getInstance(FileFinderScopeEnum::SINGLE)->find($id, TypeEnum::IMAGE);

            if ($image === null) {
                return '';
            }
        }

        return $this->urlResolver->size($image, $sizeName);
    }

    public function gallery(array $ids, array $params = []): string
    {
        $finder = $this->finderFactory->getInstance(FileFinderScopeEnum::SINGLE);
        $finder->setCriteria([
            'id__in' => $ids,
            'type'   => TypeEnum::IMAGE,
            'order_by'  => 'id',
            'order_dir' => $ids
        ]);
        $finder->fetch();

        $images = $finder->getResult();

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

    public function svg(string $id, $params = []): string
    {
        $svg = $this->finderFactory->getInstance(FileFinderScopeEnum::SINGLE)->find($id, TypeEnum::SVG);

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
            $svg = $this->finderFactory->getInstance(FileFinderScopeEnum::SINGLE)->find($id, TypeEnum::SVG);

            if ($svg === null) {
                return '';
            }
        }

        return '/' . $svg->getPath() . '/' . $svg->getFilename();
    }

    public function isFileType(string $id, string $type): bool
    {
        return (bool) $this->finderFactory->getInstance(FileFinderScopeEnum::SINGLE)->find($id, $type);
    }
}
