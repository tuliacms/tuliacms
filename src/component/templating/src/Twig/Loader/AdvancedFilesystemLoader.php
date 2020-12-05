<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Twig\Loader;

use Tulia\Component\Templating\ViewFilter\FilterInterface;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * @author Adam Banaszkiewicz
 */
class AdvancedFilesystemLoader implements LoaderInterface
{
    protected $paths = [];
    protected $cache = [];
    protected $errorCache = [];

    private $rootPath;
    private $filter;

    public function __construct(FilterInterface $filter, $paths = [], string $rootPath = null)
    {
        $this->rootPath = ($rootPath ?? getcwd()).\DIRECTORY_SEPARATOR;
        if (false !== $realPath = realpath($this->rootPath)) {
            $this->rootPath = $realPath.\DIRECTORY_SEPARATOR;
        }

        foreach ($paths as $prefix => $path) {
            $this->setPath($prefix, $path);
        }

        $this->filter = $filter;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    public function getPrefixes(): array
    {
        return array_keys($this->paths);
    }

    public function setPath(string $prefix, string $path): void
    {
        $this->paths[$prefix] = $path;
    }

    /**
     * @throws LoaderError
     */
    public function prependPath(string $path, string $prefix): void
    {
        // invalidate the cache
        $this->cache = $this->errorCache = [];

        $checkPath = $this->isAbsolutePath($path) ? $path : $this->rootPath.$path;
        if (!is_dir($checkPath)) {
            throw new LoaderError(sprintf('The "%s" directory does not exist ("%s").', $path, $checkPath));
        }

        $this->paths[$prefix] = rtrim($path, '/\\');
    }

    public function getSourceContext(string $name): Source
    {
        if (null === $path = $this->findTemplate($name)) {
            return new Source('', $name, '');
        }

        return new Source(file_get_contents($path), $name, $path);
    }

    public function getCacheKey(string $name): string
    {
        if (null === $path = $this->findTemplate($name)) {
            return '';
        }
        $len = \strlen($this->rootPath);
        if (0 === strncmp($this->rootPath, $path, $len)) {
            return substr($path, $len);
        }

        return $path;
    }

    /**
     * @return bool
     */
    public function exists(string $name)
    {
        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return true;
        }

        return null !== $this->findTemplate($name, false);
    }

    public function isFresh(string $name, int $time): bool
    {
        // false support to be removed in 3.0
        if (null === $path = $this->findTemplate($name)) {
            return false;
        }

        return filemtime($path) < $time;
    }

    /**
     * @return string|null
     */
    protected function findTemplate(string $name, bool $throw = true)
    {
        $name = $this->normalizeName($name);

        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (isset($this->errorCache[$name])) {
            if (!$throw) {
                return null;
            }

            throw new LoaderError($this->errorCache[$name]);
        }

        $names = $this->filter->filter($name);

        $lastException = null;

        foreach ($names as $namePrepared) {
            try {
                $this->validateName($namePrepared);

                [$prefix, $shortname] = $this->parseName($namePrepared);

                if ($path = $this->getViewPathname($namePrepared, $prefix, $shortname, $throw)) {
                    return $path;
                }
            } catch (LoaderError $e) {
                $lastException = $e;
            }
        }

        if ($lastException) {
            if (!$throw) {
                return null;
            }

            throw $e;
        }
    }

    private function getViewPathname(string $name, string $prefix, string $shortname, bool $throw): ?string
    {
        if (!isset($this->paths[$prefix])) {
            $this->errorCache[$name] = sprintf('There are no registered paths for prefix "%s".', $prefix);

            if (!$throw) {
                return null;
            }

            throw new LoaderError($this->errorCache[$name]);
        }

        if (isset($this->paths[$prefix])) {
            $path = $this->paths[$prefix];

            if (!$this->isAbsolutePath($path)) {
                $path = $this->rootPath.$path;
            }

            if (is_file($path.$shortname)) {
                if (false !== $realpath = realpath($path.$shortname)) {
                    return $this->cache[$name] = $realpath;
                }

                return $this->cache[$name] = $path.$shortname;
            }
        }

        $this->errorCache[$name] = sprintf('Unable to find template "%s" (looked into: %s).', $name, $this->paths[$prefix]);

        if (!$throw) {
            return null;
        }

        throw new LoaderError($this->errorCache[$name]);
    }

    private function normalizeName(string $name): string
    {
        return preg_replace('#/{2,}#', '/', str_replace('\\', '/', $name));
    }

    private function parseName(string $name): array
    {
        if (isset($name[0]) && '@' === $name[0]) {
            if (false === strpos($name, '/')) {
                throw new LoaderError(sprintf('Malformed prefixed template name "%s" (expecting "@prefix/template_name").', $name));
            }

            $shortname = null;

            foreach (array_keys($this->paths) as $prefix) {
                if (strpos($name, $prefix) === 0) {
                    $shortname = str_replace($prefix, '', $name);
                    break;
                }
            }

            if ($shortname) {
                return [$prefix, $shortname];
            }
        }

        throw new LoaderError(sprintf('Malformed prefixed template name "%s" (expecting "@prefix/template_name").', $name));
    }

    private function validateName(string $name): void
    {
        if (false !== strpos($name, "\0")) {
            throw new LoaderError('A template name cannot contain NUL bytes.');
        }

        $name = ltrim($name, '/');
        $parts = explode('/', $name);
        $level = 0;
        foreach ($parts as $part) {
            if ('..' === $part) {
                --$level;
            } elseif ('.' !== $part) {
                ++$level;
            }

            if ($level < 0) {
                throw new LoaderError(sprintf('Looks like you try to load a template outside configured directories (%s).', $name));
            }
        }
    }

    private function isAbsolutePath(string $file): bool
    {
        return strspn($file, '/\\', 0, 1)
            || (\strlen($file) > 3 && ctype_alpha($file[0])
                && ':' === $file[1]
                && strspn($file, '/\\', 2, 1)
            )
            || null !== parse_url($file, PHP_URL_SCHEME)
            ;
    }
}
