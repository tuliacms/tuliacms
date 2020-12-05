<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Breadcrumbs\Application\GeneratorInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class BreadcrumbsExtension extends AbstractExtension
{
    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var DocumentInterface
     */
    protected $document;

    /**
     * @param GeneratorInterface $generator
     * @param DocumentInterface $document
     */
    public function __construct(
        GeneratorInterface $generator,
        DocumentInterface $document
    ) {
        $this->generator = $generator;
        $this->document  = $document;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('breadcrumbs', function ($context) {
                $breadcrumbs = $this->generator->generateFromRequest($context['app']->getRequest());

                // Append current page placeholder if at least
                // homepage crumb is in collection.
                if ($breadcrumbs->count() <= 1) {
                    $breadcrumbs->push('#', $this->document->getTitle());
                }

                return $breadcrumbs;
            }, [
                'needs_context' => true,
                'is_safe' => [ 'html' ],
            ]),
        ];
    }
}
