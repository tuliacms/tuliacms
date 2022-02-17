<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain\ReadModel;

use Tulia\Cms\Shared\Infrastructure\Persistence\Doctrine\DBAL\ConnectionInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TaxonomyBreadcrumbsReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalTaxonomyBreadcrumbsReadStorage implements TaxonomyBreadcrumbsReadStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $termId, string $websiteId, string $locale, string $defaultLocale): array
    {
        return $this->connection->fetchAllAssociative("
WITH RECURSIVE tree_path (
    id,
    website_id,
    type,
    is_root,
    parent_id,
    position,
    level,
    count,
    locale,
    title,
    slug,
    visibility,
    generated_path
) AS (
    SELECT
        id,
        website_id,
        type,
        is_root,
        parent_id,
        position,
        level,
        count,
        :defaultLocale AS locale,
        title,
        slug,
        visibility,
        CONCAT(title, '/') as generated_path
    FROM #__term
    WHERE
        id = :term_id
        AND website_id = :websiteId
UNION ALL
    SELECT
            tm.id,
            tm.website_id,
            tm.type,
            tm.is_root,
            tm.parent_id,
            tm.position,
            tm.level,
            tm.count,
            COALESCE(tl.locale, :defaultLocale) AS locale,
            COALESCE(tl.title, tm.title) AS title,
            COALESCE(tl.slug, tm.slug) AS slug,
            COALESCE(tl.visibility, tm.visibility) AS visibility,
            CONCAT(tp.generated_path, tm.title, '/') AS generated_path
        FROM tree_path AS tp
        INNER JOIN #__term AS tm
            ON tp.parent_id = tm.id
        LEFT JOIN #__term_lang AS tl
            ON tm.id = tl.term_id AND tl.locale = :locale
        WHERE
            tm.website_id = :websiteId
)
SELECT * FROM tree_path", [
            'term_id' => $termId,
            'websiteId' => $websiteId,
            'locale' => $locale,
            'defaultLocale' => $defaultLocale,
        ]);
    }
}
