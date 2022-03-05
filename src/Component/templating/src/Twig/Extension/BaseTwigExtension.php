<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * @author Adam Banaszkiewicz
 */
class BaseTwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('str_replace', function ($in, $from, $to) {
                return str_replace($from, $to, $in);
            }),
            new TwigFilter('format_bytes', function ($bytes, $mode = 'full') {
                $units = ['B', 'kB', 'MB', 'GB', 'TB'];
                $number = number_format($bytes / (1024 ** $i = floor(log($bytes, 1024))), ($i >= 1) ? 2 : 0);
                $unit = $units[$i];

                if ($mode === 'unit') {
                    return $unit;
                } elseif ($mode === 'number') {
                    return $number;
                } else {
                    return $number . ' ' . $unit;
                }
            }),
            new TwigFilter('format_php_to_momentjs', function ($format) {
                return strtr($format, [
                    'd' => 'DD',
                    'D' => 'ddd',
                    'j' => 'D',
                    'l' => 'dddd',
                    'N' => 'E',
                    'S' => 'o',
                    'w' => 'e',
                    'z' => 'DDD',
                    'W' => 'W',
                    'F' => 'MMMM',
                    'm' => 'MM',
                    'M' => 'MMM',
                    'n' => 'M',
                    't' => '', // no equivalent
                    'L' => '', // no equivalent
                    'o' => 'YYYY',
                    'Y' => 'YYYY',
                    'y' => 'YY',
                    'a' => 'a',
                    'A' => 'A',
                    'B' => '', // no equivalent
                    'g' => 'h',
                    'G' => 'H',
                    'h' => 'hh',
                    'H' => 'HH',
                    'i' => 'mm',
                    's' => 'ss',
                    'u' => 'SSS',
                    'e' => 'zz', // deprecated since version 1.6.0 of moment.js
                    'I' => '', // no equivalent
                    'O' => '', // no equivalent
                    'P' => '', // no equivalent
                    'T' => '', // no equivalent
                    'Z' => '', // no equivalent
                    'c' => '', // no equivalent
                    'r' => '', // no equivalent
                    'U' => 'X',
                ]);
            }),
        ];
    }

    public function getTests(): array
    {
        return [
            new TwigTest('boolean', function ($value) {
                return \is_bool($value);
            }),
            new TwigTest('bool', function ($value) {
                return \is_bool($value);
            }),
            new TwigTest('object', function ($value) {
                return \is_object($value);
            }),
            new TwigTest('numeric', function ($value) {
                return is_numeric($value);
            }),
            new TwigTest('string', function ($value) {
                return \is_string($value);
            }),
            new TwigTest('float', function ($value) {
                return \is_float($value);
            }),
            new TwigTest('resource', function ($value) {
                return \is_resource($value);
            }),
            new TwigTest('null', function ($value) {
                return $value === null;
            })
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('dump', function (array $values = []) {
                ob_start();
                dump(...$values);
                $content = ob_get_clean();

                $regex = "/<script[^>]*>(.*)<\/script>/Uis";

                preg_match_all($regex, $content, $scripts);

                /*foreach($scripts[0] as $script)
                {
                    $updated = str_replace('<script>', '<script nonce="'.$this->createCspNonce().'">', $script);
                    $content = str_replace($script, $updated, $content);
                }*/

                return $content;
            }, [
                'is_safe' => [ 'html' ],
                'is_variadic' => true
            ]),
            new TwigFunction('get_class', function ($value) {
                return \get_class($value);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('str_repeat', function ($input, $multiplier) {
                return str_repeat($input, $multiplier);
            }),
            new TwigFunction('str_replace', function ($from, $to, $in) {
                return str_replace($from, $to, $in);
            }),
            new TwigFunction('md5', function (string $string, bool $rawOutput = false) {
                return md5($string, $rawOutput);
            }),
            new TwigFunction('uniqid', function (string $prefix = '', bool $moreEntropy = false) {
                return uniqid($prefix, $moreEntropy);
            }),
            new TwigFunction('relative', function ($context, $base, $relative) {
                if (is_file($base) || strncmp($base, '@', 1) === 0) {
                    $path = pathinfo($base, PATHINFO_DIRNAME);
                } else {
                    $path = $base;
                }

                return $path.'/'.ltrim($relative, '/');
            }, [
                'needs_context' => true,
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('render_string', function (Environment $env, string $template, array $data) {
                return $env->createTemplate($template)->render($data);
            }, [
                'is_safe' => [ 'html' ],
                'needs_environment' => true,
            ]),
        ];
    }
}
