<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Infrastructure\Framework\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('attribute', function (Environment $env, $attribute, array $data = []) {
                if ($attribute === null || $attribute === '') {
                    return '';
                }

                $attribute = (string) $attribute;

                if ($this->hasTwigTags($attribute) === false) {
                    return $attribute;
                }

                return $env->createTemplate($attribute)->render($data);
            }, [
                'is_safe' => [ 'html' ],
                'needs_environment' => true,
            ]),
        ];
    }

    private function hasTwigTags(string $template): bool
    {
        $echoOpen = strpos($template, '{{');
        $echoClose = strpos($template, '}}');
        $execOpen = strpos($template, '{%');
        $execClose = strpos($template, '%}');

        return ($echoOpen >= 0 && $echoClose >= 0) || ($execOpen >= 0 && $execClose >= 0);
    }
}
