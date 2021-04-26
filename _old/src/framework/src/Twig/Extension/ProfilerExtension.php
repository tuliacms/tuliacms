<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Adam Banaszkiewicz
 */
class ProfilerExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $projectDir;

    /**
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('abbr_class', function (?string $classname) {
                $parts = explode('\\', $classname);
                return '<abbr title="' . $classname . '">' . end($parts) . '</abbr>';
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFilter('file_link', function (?string $file, ?int $line) {
                $file = str_replace(\dirname($this->projectDir, 1), '', $file);
                return "phpstorm://open?file=$file&line=$line";
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
