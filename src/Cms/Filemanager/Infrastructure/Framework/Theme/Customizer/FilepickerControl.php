<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Infrastructure\Framework\Theme\Customizer;

use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;
use Tulia\Component\Theme\Customizer\Builder\Rendering\Controls\AbstractControl;

/**
 * @author Adam Banaszkiewicz
 */
class FilepickerControl extends AbstractControl
{
    protected EngineInterface $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function build(array $params): string
    {
        return $this->engine->render(new View('@backend/filemanager/customizer/filepicker.tpl', $params));
    }

    public static function getName(): string
    {
        return 'filepicker';
    }
}
