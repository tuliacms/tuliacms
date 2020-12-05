<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Shared\Pagination;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Paginator extends \JasonGrimes\Paginator
{
    const NUM_PLACEHOLDER = '__PAGE__';

    /**
     * @var Request
     */
    private $request;

    protected $maxPagesToShow = 5;
    protected $previousText = '&laquo;';
    protected $nextText = '&raquo;';
    protected $ulClasses = [
        'pagination'
    ];

    public function __construct(Request $request, int $total, int $page = null, int $perPage = null)
    {
        $this->request = $request;

        $page = $page ?: (int) $this->request->query->get('page', 1);
        $page = $page <= 0 ? 1 : $page;

        $perPage = $perPage ?: (int) $this->request->query->get('perPage', 10);
        $perPage = $perPage <= 0 ? 10 : $perPage;

        parent::__construct($total, $perPage, $page, $this->getUrlPattern());
    }

    public function getStart(): int
    {
        return $this->getCurrentPage() === 0 ? 0 : ($this->getCurrentPage() - 1) * $this->getItemsPerPage();
    }

    public function getLimit(): int
    {
        return $this->getItemsPerPage();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageUrl($page): string
    {
        return str_replace(self::NUM_PLACEHOLDER, $page, $this->urlPattern);
    }

    public function getUrlPattern(): string
    {
        if ($this->urlPattern) {
            return $this->urlPattern;
        }

        $query = $this->request->query->all();
        $query['page'] = self::NUM_PLACEHOLDER;

        $request = $this->request->duplicate($query);

        return $this->urlPattern = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo() . '?' . http_build_query($query);
    }

    public function toHtml(): string
    {
        if ($this->numPages <= 1) {
            return '';
        }

        $html = '<ul class="' . implode(' ', $this->ulClasses) . '">';

        if ($this->getPrevUrl()) {
            $html .= '<li class="page-item page-prev"><a href="' . $this->getPrevUrl() . '" class="page-link">' . $this->previousText . '</a></li>';
        }

        foreach ($this->getPages() as $page) {
            if ($page['url']) {
                $html .= '<li class="page-item' . ($page['isCurrent'] ? ' active' : '') . '"><a href="' . $page['url'] . '" class="page-link">' . $page['num'] . '</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span class="page-link">' . $page['num'] . '</span></li>';
            }
        }

        if ($this->getNextUrl()) {
            $html .= '<li class="page-item page-next"><a href="' . $this->getNextUrl() . '" class="page-link">' . $this->nextText . '</a></li>';
        }

        $html .= '</ul>';

        return $html;
    }

    public function position(string $position): self
    {
        switch ($position) {
            case 'right' : $position = 'justify-content-end'; break;
            case 'center': $position = 'justify-content-center'; break;
            default: $position = ''; break;
        }

        $this->ulClasses['position'] = $position;

        return $this;
    }

    public function addClass(string $class): self
    {
        $this->ulClasses[] = $class;

        return $this;
    }
}
