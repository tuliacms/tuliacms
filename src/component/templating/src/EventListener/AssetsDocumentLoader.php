<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\EventListener;

use Requtize\Assetter\AssetterInterface;
use Requtize\Assetter\Exception\MissingAssetException;

/**
 * @author Adam Banaszkiewicz
 */
class AssetsDocumentLoader
{
    /**
     * @var AssetterInterface
     */
    protected $assetter;

    /**
     * @param AssetterInterface $assetter
     */
    public function __construct(AssetterInterface $assetter)
    {
        $this->assetter = $assetter;
    }

    /**
     * Returns all loaded assets to this point, save list of names of these libs
     * and clears loading to next call.
     *
     * @return string
     *
     * @throws MissingAssetException
     */
    public function loadThemeHead(): string
    {
        return $this->assetter->build('head')->all();
    }

    /**
     * Removes duplicates between loadThemeHead() and this method call,
     * and returns new assets.
     *
     * @return string
     *
     * @throws MissingAssetException
     */
    public function loadThemeBody(): string
    {
        return $this->assetter->build()->all();
    }
}
