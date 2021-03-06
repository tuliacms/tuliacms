<?php

declare(strict_types=1);

namespace Tulia\Cms\Breadcrumbs\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Breadcrumbs\Domain\BreadcrumbsGeneratorInterface;
use Tulia\Cms\Platform\Shared\Document\DocumentInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class BreadcrumbsExtension extends AbstractExtension
{
    protected BreadcrumbsGeneratorInterface $generator;

    protected DocumentInterface $document;

    public function __construct(
        BreadcrumbsGeneratorInterface $generator,
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
