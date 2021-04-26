<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Framework\Theme\Customizer;

use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Theme\Customizer\Builder\Controls\AbstractControl;

/**
 * @author Adam Banaszkiewicz
 */
class FilepickerControl extends AbstractControl
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
    public function build(array $params): string
    {
        return $this->engine->render(new View('@filemanager/customizer/filepicker.tpl', $params));
    }

    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'filepicker';
    }
}
