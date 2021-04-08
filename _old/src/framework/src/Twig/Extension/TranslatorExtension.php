<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class TranslatorExtension extends AbstractExtension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('trans', function (?string $id, array $parameters = [], $domain = null, $locale = null) {
                return $this->translator->trans($id, $parameters, $domain, $locale);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('trans', function (?string $id, array $parameters = [], $domain = null, $locale = null) {
                return $this->translator->trans($id, $parameters, $domain, $locale);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
