<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Utils\Slug;

use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;

/**
 * @author Adam Banaszkiewicz
 */
class AusiSlugGenerator implements SluggerInterface
{
    /**
     * @var SlugGenerator
     */
    protected $generator;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @param $locale
     */
    public function __construct($locale)
    {
        $this->locale = (string) $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function url($input, string $separator = '-', string $locale = null): ?string
    {
        if (\is_array($input) === false) {
            $input = [ $input ];
        }

        foreach ($input as $part) {
            if (\is_string($part) === false) {
                continue;
            }

            $slug = $this->instance()->generate($part);

            if ($slug) {
                return $slug;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function filename($input, string $separator = '-'): ?string
    {
        return $this->url($input, $separator);
    }

    /**
     * @return SlugGenerator
     */
    protected function instance(): SlugGenerator
    {
        if ($this->generator) {
            return $this->generator;
        }

        return $this->generator = new SlugGenerator((new SlugOptions())
            ->setValidChars('a-z0-9')
            ->setLocale($this->locale)
            ->setDelimiter('-')
        );
    }
}
