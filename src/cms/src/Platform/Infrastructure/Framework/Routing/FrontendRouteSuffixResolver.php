<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Routing;

use Tulia\Cms\Options\Application\Service\Options;

/**
 * @author Adam Banaszkiewicz
 */
class FrontendRouteSuffixResolver
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @var string
     */
    private $suffix;

    /**
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function appendSuffix(string $url): string
    {
        if ($this->suffixExists($url)) {
            return $url;
        }

        return $url . $this->getSuffix();
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function removeSuffix(string $url): string
    {
        if ($this->suffixExists($url) === false) {
            return $url;
        }

        return substr($url, 0, -\strlen($this->getSuffix()));
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function suffixExists(string $url): bool
    {
        return substr($url, -\strlen($this->getSuffix())) === $this->getSuffix();
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        if ($this->suffix) {
            return $this->suffix;
        }

        return $this->suffix = (string) $this->options->get('url_suffix');
    }
}
