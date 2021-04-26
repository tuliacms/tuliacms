<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\Application\Requirements;

/**
 * @author Adam Banaszkiewicz
 */
class Requirements
{
    public static function getRequirements(string $rootDir): array
    {
        $requirements = [];

        static::requirePHP($requirements);
        static::requirePdo($requirements);
        static::requireMbstring($requirements);
        static::requireZip($requirements);
        static::requireCurl($requirements);
        static::requireDirectoriesWritable($requirements, $rootDir);

        return $requirements;
    }

    public static function requirementsMet(array $requirements): bool
    {
        $met = true;

        foreach ($requirements as $requirement) {
            if ($requirement->status === Requirement::STATUS_REQUIRE) {
                $met = false;
            }
        }

        return $met;
    }

    public static function requirePHP(array &$requirements): void
    {
        $requirement = new Requirement();
        $requirement->name = 'PHP version';
        if (PHP_VERSION_ID > 70300 === false) {
            $requirement->status   = Requirement::STATUS_REQUIRE;
            $requirement->cause    = sprintf('PHP version is %s', PHP_VERSION);
            $requirement->solution = 'Required minimum PHP version is <b>7.3</b>. Please upgrade it to install Tulia CMS';
        } else {
            $requirement->status = Requirement::STATUS_PASSED;
            $requirement->cause  = sprintf('PHP version is %s', PHP_VERSION);
        }
        $requirements[] = $requirement;
    }

    public static function requirePdo(array &$requirements): void
    {
        $requirement = new Requirement();
        $requirement->name = 'PDO extension';
        if (\extension_loaded('pdo') === false) {
            $requirement->status   = Requirement::STATUS_REQUIRE;
            $requirement->cause    = 'PDO extension not installed';
            $requirement->solution = 'Install or enable PDO extension in PHP';
        } else {
            if (\in_array('mysql', \PDO::getAvailableDrivers(), true) === false) {
                $requirement->status   = Requirement::STATUS_REQUIRE;
                $requirement->cause    = 'Missing pdo_mysql driver.';
                $requirement->solution = 'Install or enable pdo_mysql driver for PDO';
            } else {
                $requirement->status = Requirement::STATUS_PASSED;
                $requirement->cause =  'PDO extension installed';
            }
        }
        $requirements[] = $requirement;
    }

    public static function requireMbstring(array &$requirements): void
    {
        $requirement = new Requirement();
        $requirement->name = 'MBString extension';
        if (\extension_loaded('mbstring') === false) {
            $requirement->status   = Requirement::STATUS_REQUIRE;
            $requirement->cause    = 'MBString extension not installed';
            $requirement->solution = 'Install or enable MBString extension in PHP';
        } else {
            $requirement->status = Requirement::STATUS_PASSED;
            $requirement->cause  = 'MBString extension installed';
        }
        $requirements[] = $requirement;
    }

    public static function requireZip(array &$requirements): void
    {
        $requirement = new Requirement();
        $requirement->name = 'ZIP extension';
        if (\extension_loaded('zip') === false) {
            $requirement->status   = Requirement::STATUS_REQUIRE;
            $requirement->cause    = 'ZIP extension not installed';
            $requirement->solution = 'Install or enable ZIP extension in PHP';
        } else {
            $requirement->status = Requirement::STATUS_PASSED;
            $requirement->cause  = 'ZIP extension installed';
        }
        $requirements[] = $requirement;
    }

    public static function requireCurl(array &$requirements): void
    {
        $requirement = new Requirement();
        $requirement->name = 'cURL extension';
        if (\extension_loaded('curl') === false) {
            $requirement->status   = Requirement::STATUS_REQUIRE;
            $requirement->cause    = 'cURL extension not installed';
            $requirement->solution = 'Install or enable cURL extension in PHP';
        } else {
            $requirement->status = Requirement::STATUS_PASSED;
            $requirement->cause  = 'cURL extension installed';
        }
        $requirements[] = $requirement;
    }

    public static function requireDirectoriesWritable(array &$requirements, string $rootDir): void
    {
        $requirement = new Requirement();
        $requirement->name = '<code>/var</code> directory';
        if (is_writable($rootDir . '/var') === false) {
            $requirement->status   = Requirement::STATUS_WARNING;
            $requirement->cause    = '<code>/var</code> is not writable';
            $requirement->solution = 'Change <code>/var</code> directory to writable, if You want to update system through the Administration Panel.';
        } else {
            $requirement->status = Requirement::STATUS_PASSED;
            $requirement->cause  = '<code>/var</code> is writable.';
        }
        $requirements[] = $requirement;

        $requirement = new Requirement();
        $requirement->name = '<code>/public</code> directory';
        if (is_writable($rootDir . '/public') === false) {
            $requirement->status   = Requirement::STATUS_REQUIRE;
            $requirement->cause    = '<code>/public</code> is not writable';
            $requirement->solution = 'Change <code>/public</code> directory to writable';
        } else {
            $requirement->status = Requirement::STATUS_PASSED;
            $requirement->cause  = '<code>/public</code> is writable.';
        }
        $requirements[] = $requirement;

        $requirement = new Requirement();
        $requirement->name = '<code>/extension</code> directory';
        if (is_writable($rootDir . '/extension') === false) {
            $requirement->status   = Requirement::STATUS_WARNING;
            $requirement->cause    = '<code>/extension</code> is not writable';
            $requirement->solution = 'Change <code>/extension</code> directory to writable, if You want to install extensions through Administration Panel.';
        } else {
            $requirement->status = Requirement::STATUS_PASSED;
            $requirement->cause  = '<code>/extension</code> is writable.';
        }
        $requirements[] = $requirement;
    }
}
