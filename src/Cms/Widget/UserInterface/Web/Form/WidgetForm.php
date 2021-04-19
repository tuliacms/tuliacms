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
class WidgetForm extends AbstractType
{
    /**
     * @var ThemeManager
     */
    protected $themeManager;

    /**
     * @param ThemeManager $themeManager
     */
    public function __construct(ThemeManager $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $theme = $this->themeManager->getTheme();
        $spaces = [];

        if ($theme->hasConfig()) {
            $spaces = $theme->getConfig()->getRegisteredWidgetSpaces();
            $spaces = array_combine(
                array_map(function ($item) {
                    return $item['label'];
                }, $spaces),
                array_map(function ($item) {
                    return $item['name'];
                }, $spaces),
            );
        }

        $builder
            ->add('id', Type\HiddenType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Uuid(),
                ],
            ])
            ->add('name', Type\TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('space', Type\ChoiceType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => $spaces ]),
                ],
                'choices' => $spaces,
                'choice_translation_domain' => false,
            ])
            ->add('save', FormType\SubmitType::class)
            ->add('cancel', FormType\CancelType::class, [
                'route' => 'backend.widget',
            ])
            ->add('widget_configuration', $options['widget_form'], $options['widget_configuration']);

        if ($options['form_extension_manager'] instanceof ManagerInterface) {
            $options['form_extension_manager']->buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'widget_form'            => null,
            'widget_configuration'   => [],
            'form_extension_manager' => null,
        ]);
    }
}
