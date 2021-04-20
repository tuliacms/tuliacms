<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Utils\Slug;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface as SymfonySluggerInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Slug\SluggerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyStringGenerator implements SluggerInterface
{
    /**
     * @var SymfonySluggerInterface
     */
    protected $slugger;

    /**
     * @var string[]
     */
    protected $allowedChars = [
        'A-Za-z0-9\-',
    ];

    /**
     * @param SymfonySluggerInterface|null $slugger
     */
    public function __construct(SymfonySluggerInterface $slugger = null)
    {
        $this->slugger = $slugger ?? new AsciiSlugger();
    }

    /**
     * {@inheritdoc}
     */
    public function url($input, string $separator = '-', string $locale = null): ?string
    {
        return strtolower((string) $this->slugger->slug($input, $separator));
    }

    /**
     * {@inheritdoc}
     */
    public function filename($input, string $separator = '-'): ?string
    {
        return strtolower((string) $this->slugger->slug($input, $separator));
    }
}
