<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;
use Tulia\Component\Theme\ManagerInterface as ThemeManager;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

/**
 * @author Adam Banaszkiewicz
 */
class ScopeEnum
{
    public const BACKEND_EDIT = 'backend.edit';
}
