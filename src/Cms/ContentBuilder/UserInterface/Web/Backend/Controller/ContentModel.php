<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Tulia\Cms\ContentBuilder\Domain\NodeType\Service\NodeTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\TaxonomyType\Service\TaxonomyTypeRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentModel extends AbstractController
{
    private NodeTypeRegistry $nodeTypeRegistry;
    private TaxonomyTypeRegistry $taxonomyTypeRegistry;

    public function __construct(
        NodeTypeRegistry $nodeTypeRegistry,
        TaxonomyTypeRegistry $taxonomyTypeRegistry
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
        $this->taxonomyTypeRegistry = $taxonomyTypeRegistry;
    }

    public function index(): ViewInterface
    {
        return $this->view('@backend/content_builder/index.tpl', [
            'nodeTypeList' => $this->nodeTypeRegistry->all(),
            'taxonomyTypeList' => $this->taxonomyTypeRegistry->all(),
        ]);
    }
}
