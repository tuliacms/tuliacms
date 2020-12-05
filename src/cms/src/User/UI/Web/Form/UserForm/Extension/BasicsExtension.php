<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UI\Web\Form\UserForm\Extension;

use Tulia\Cms\User\UI\Web\Form\Extension\AbstractBasicsExtension;

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
