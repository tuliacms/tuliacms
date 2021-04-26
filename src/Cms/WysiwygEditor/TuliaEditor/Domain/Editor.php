<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\TuliaEditor\Domain;

use Tulia\Cms\WysiwygEditor\Core\Application\AbstractWysiwygEditor;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Editor extends AbstractWysiwygEditor
{
    /**
     * @var EngineInterface
     */
    protected $engine;

    /**
     * @param EngineInterface $engine
     */
    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'tulia-editor';
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Tulia Editor';
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $name, ?string $content = null, array $params = []): string
    {
        if (isset($params['id']) === false) {
            $params['id'] = uniqid('', true);
        }

        return $this->engine->render(new View('@backend/wysiwyg-editor/tulia-editor/editor.tpl', [
            'name'    => $name,
            'content' => $content,
            'params'  => $params,
        ]));
    }
}
