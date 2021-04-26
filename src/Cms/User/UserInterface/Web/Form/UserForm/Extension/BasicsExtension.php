<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form\UserForm\Extension;

use Tulia\Cms\User\UserInterface\Web\Form\Extension\AbstractBasicsExtension;

/**
 * @author Adam Banaszkiewicz
 */
class BasicsExtension extends AbstractBasicsExtension
{
    /**
     * {@inheritdoc}
     */
    public function getSections(): array
    {
        $sections = parent::getSections();

        foreach ($sections as $section) {
            $section->setGroup('sidebar');
        }

        return $sections;
    }
}
