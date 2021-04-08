<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Slug;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleSlugGenerator implements SluggerInterface
{
    public static $langSignsReplacement = [
        '/ä|æ|ǽ/' => 'ae',
        '/ö|œ/' => 'oe',
        '/ü/' => 'ue',
        '/Ä/' => 'Ae',
        '/Ü/' => 'Ue',
        '/Ö/' => 'Oe',
        '/À|Á|Â|Ã|Ä|Å|Ǻ|Ā|Ă|Ą|Ǎ/' => 'A',
        '/à|á|â|ã|å|ǻ|ā|ă|ą|ǎ|ª/' => 'a',
        '/Ç|Ć|Ĉ|Ċ|Č/' => 'C',
        '/ç|ć|ĉ|ċ|č/' => 'c',
        '/Ð|Ď|Đ/' => 'D',
        '/ð|ď|đ/' => 'd',
        '/È|É|Ê|Ë|Ē|Ĕ|Ė|Ę|Ě/' => 'E',
        '/è|é|ê|ë|ē|ĕ|ė|ę|ě/' => 'e',
        '/Ĝ|Ğ|Ġ|Ģ/' => 'G',
        '/ĝ|ğ|ġ|ģ/' => 'g',
        '/Ĥ|Ħ/' => 'H',
        '/ĥ|ħ/' => 'h',
        '/Ì|Í|Î|Ï|Ĩ|Ī|Ĭ|Ǐ|Į|İ/' => 'I',
        '/ì|í|î|ï|ĩ|ī|ĭ|ǐ|į|ı/' => 'i',
        '/Ĵ/' => 'J',
        '/ĵ/' => 'j',
        '/Ķ/' => 'K',
        '/ķ/' => 'k',
        '/Ĺ|Ļ|Ľ|Ŀ|Ł/' => 'L',
        '/ĺ|ļ|ľ|ŀ|ł/' => 'l',
        '/Ñ|Ń|Ņ|Ň/' => 'N',
        '/ñ|ń|ņ|ň|ŉ/' => 'n',
        '/Ò|Ó|Ô|Õ|Ō|Ŏ|Ǒ|Ő|Ơ|Ø|Ǿ/' => 'O',
        '/ò|ó|ô|õ|ō|ŏ|ǒ|ő|ơ|ø|ǿ|º/' => 'o',
        '/Ŕ|Ŗ|Ř/' => 'R',
        '/ŕ|ŗ|ř/' => 'r',
        '/Ś|Ŝ|Ş|Š/' => 'S',
        '/ś|ŝ|ş|š|ſ/' => 's',
        '/Ţ|Ť|Ŧ/' => 'T',
        '/ţ|ť|ŧ/' => 't',
        '/Ù|Ú|Û|Ũ|Ū|Ŭ|Ů|Ű|Ų|Ư|Ǔ|Ǖ|Ǘ|Ǚ|Ǜ/' => 'U',
        '/ù|ú|û|ũ|ū|ŭ|ů|ű|ų|ư|ǔ|ǖ|ǘ|ǚ|ǜ/' => 'u',
        '/Ý|Ÿ|Ŷ/' => 'Y',
        '/ý|ÿ|ŷ/' => 'y',
        '/Ŵ/' => 'W',
        '/ŵ/' => 'w',
        '/Ź|Ż|Ž/' => 'Z',
        '/ź|ż|ž/' => 'z',
        '/Æ|Ǽ/' => 'AE',
        '/ß/'=> 'ss',
        '/Ĳ/' => 'IJ',
        '/ĳ/' => 'ij',
        '/Œ/' => 'OE',
        '/ƒ/' => 'f',
        '/|`|~|!|@|#|$|%|^|&|*|(|)|_|+|-|=|{|}|[|]|:|"|\||;|\'|\|<|>|?|,|.|\//' => '-'
    ];

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

            if (empty($part)) {
                continue;
            }

            $slug = $this->slugify($part, $separator);

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
     * @param string $input
     * @param string $separator
     *
     * @return string
     */
    public function slugify(string $input, string $separator = '-'): string
    {
        $input = htmlspecialchars_decode($input, ENT_QUOTES);
        $input = $this->sanitizeLanguageSigns($input);
        $input = preg_replace('/[\s]+/', $separator, trim($input));
        //$input = preg_replace("/[^\pL0-9_\s]/", $separator, $input);
        $input = preg_replace("/[{$separator}]+/", $separator, $input);
        $input = mb_strtolower($input, 'UTF-8');
        $input = trim($input);

        return trim($input, '-');
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public function sanitizeLanguageSigns(string $input): string
    {
        $notAllowed = array_keys(static::$langSignsReplacement);
        $allowed    = array_values(static::$langSignsReplacement);

        $a = [];
        $b = [];

        for($i = 0, $count = count($allowed); $i < $count; $i++)
        {
            $c = substr($notAllowed[$i], 1, (strlen($notAllowed[$i]) - 2));
            $c = explode('|', $c);

            foreach($c as $char)
            {
                $a[] = $char;
                $b[] = $allowed[$i];
            }
        }

        return str_replace($a, $b, $input);
    }
}
