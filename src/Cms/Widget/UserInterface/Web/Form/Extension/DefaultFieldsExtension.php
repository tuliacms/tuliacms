<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UserInterface\Web\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Cms\Widget\UserInterface\Web\Form\WidgetForm;
use Tulia\Component\FormBuilder\Extension\AbstractExtension;
use Tulia\Component\FormBuilder\Section\SectionsBuilderInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultFieldsExtension extends AbstractExtension
{
    protected ManagerInterface $themeManager;

    public function __construct(ManagerInterface $themeManager)
    {
        $this->themeManager = $themeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $theme = $this->themeManager->getTheme();
        $widgetStyles = [];

        if ($theme->hasConfig()) {
            $widgetStyles = $theme->getConfig()->getRegisteredWidgetStyles();
            $widgetStyles = array_combine(
                array_map(function ($item) {
                    return $item['label'];
                }, $widgetStyles),
                array_keys($widgetStyles),
            );
        }

        $builder
            ->add('html_class', Type\TextType::class)
            ->add('html_id', Type\TextType::class)
            ->add('title', Type\TextType::class, [
                'help' => 'titleDescription',
                'translation_domain' => 'widgets',
            ])
            ->add('styles', Type\ChoiceType::class, [
                'help' => 'stylesDescription',
                'constraints' => [
                    //new Assert\Choice([ 'choices' => $widgetStyles ]),
                ],
                'multiple' => true,
                'choices' => $widgetStyles,
                'translation_domain' => 'widgets',
            ])
            ->add('visibility', FormType\YesNoType::class, [
                'label' => 'visibility',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(SectionsBuilderInterface $builder): void
    {
        $builder
            ->add('status', [
                'translation_domain' => 'widgets',
                'group' => 'sidebar',
                'priority' => 1000,
                'fields' => ['visibility'],
            ])
            ->add('look', [
                'view' => '@backend/widget/parts/look.tpl',
                'translation_domain' => 'widgets',
                'priority' => 800,
                'group' => 'sidebar',
                'fields' => ['html_class', 'html_id', 'title', 'styles'],
            ])
            // @todo Finish this feld
            /*->add('widget'', [
                'label' => 'widgetOptions',
                'template' => '{% if widgetView %}
            {% include widgetView.views|first with widgetView.data|merge({form: form.widget_configuration}) %}
        {% endif %}',
                'translation_domain' => 'widgets',
                'priority' => 1000,
                'fields' => ['{% set fields = [] %}
            {% for key, item in form.widget_configuration %}
                {% set fields = fields|merge([key]) %}
            {% endfor %}']
            ])*/
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return $formType instanceof WidgetForm;
    }
}
