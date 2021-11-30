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
class NodeType extends AbstractController
{
    private NodeTypeRegistry $nodeTypeRegistry;

    public function __construct(
        NodeTypeRegistry $nodeTypeRegistry
    ) {
        $this->nodeTypeRegistry = $nodeTypeRegistry;
    }

    public function create(): ViewInterface
    {
        return $this->view('@backend/content_builder/node_type/create.tpl');
    }
}
