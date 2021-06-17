<?php

declare(strict_types=1);

namespace Tulia\Component\Routing\Tests\Unit;

use Tulia\Tests\Unit\TestCase;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Routing\Website\CurrentWebsite;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Website;
use Tulia\Component\Routing\WebsitePrefixesResolver;

/**
 * @author Adam Banaszkiewicz
 */
class WebsitePrefixesResolverTest extends TestCase
{
    public function testSmoke(): void
    {
        // Arrange
        $resolver = new WebsitePrefixesResolver($this->produceWebsite());
        // Act
        $result = $resolver->appendWebsitePrefixes('homepage', '/', []);
        // Assert
        self::assertSame('/', $result);
    }

    public function testBuildFQUrlWithDomain(): void
    {
        // Arrange
        $resolver = new WebsitePrefixesResolver($this->produceWebsite());
        // Act
        $result = $resolver->appendWebsitePrefixes('some_page', 'http://localhost/path?param=value', []);
        // Assert
        self::assertSame('http://localhost/path?param=value', $result);
    }

    public function testAppendForeignLocaleInFrontendPath(): void
    {
        // Arrange
        $resolver = new WebsitePrefixesResolver($this->produceWebsite());
        // Act
        $result = $resolver->appendWebsitePrefixes('some_page', '/path', ['_locale' => 'pl_PL']);
        // Assert
        self::assertSame('/pl/path', $result);
    }

    public function testAppendForeignLocaleInBackendPath(): void
    {
        // Arrange
        $resolver = new WebsitePrefixesResolver($this->produceWebsite());
        // Act
        $result = $resolver->appendWebsitePrefixes('backend.some_page', '/administrator/path', ['_locale' => 'pl_PL']);
        // Assert
        self::assertSame('/administrator/pl/path', $result);
    }

    public function testAppendCurrentForeignLocaleInBackendPathWhenNotSetExplicitlyInParameters(): void
    {
        // Arrange
        $locales = [];
        $locales[] = new Locale('en_US', 'localhost', null, null, SslModeEnum::ALLOWED_BOTH, true);
        $locales[] = new Locale('pl_PL', 'localhost', '/pl', null, SslModeEnum::ALLOWED_BOTH, false);

        $website = new Website('ID', $locales, $locales[1]);

        $currentWebsite = new CurrentWebsite();
        $currentWebsite->set($website);

        $resolver = new WebsitePrefixesResolver($currentWebsite);
        // Act
        $result = $resolver->appendWebsitePrefixes('backend.some_page', '/administrator/path');
        // Assert
        self::assertSame('/administrator/pl/path', $result);
    }

    private function produceWebsite(): CurrentWebsiteInterface
    {
        $defaultLocale = new Locale('en_US', 'localhost', null, null, SslModeEnum::ALLOWED_BOTH, true);
        $locales = [$defaultLocale];
        $locales[] = new Locale('pl_PL', 'localhost', '/pl', null, SslModeEnum::ALLOWED_BOTH, false);

        $website = new Website('ID', $locales, $defaultLocale);

        $currentWebsite = new CurrentWebsite();
        $currentWebsite->set($website);

        return $currentWebsite;
    }
}
