<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class DocumentExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var DocumentInterface
     */
    protected $document;

    /**
     * @param DocumentInterface $document
     */
    public function __construct(DocumentInterface $document)
    {
        $this->document = $document;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals(): array
    {
        return [
            'document' => $this->document,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('title', function ($default = '') {
                return $this->document->getTitle() ?? $default;
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
