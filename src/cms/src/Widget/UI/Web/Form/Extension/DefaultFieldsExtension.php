<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\UI\Web\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Tulia\Cms\Widget\Application\Model\Widget;
use Tulia\Cms\Widget\UI\Web\Form\ScopeEnum;
use Tulia\Component\FormBuilder\AbstractExtension;
use Tulia\Component\FormBuilder\Section\FormRowSection;
use Tulia\Component\FormBuilder\Section\Section;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultFieldsExtension extends AbstractExtension
{
    /**
     * @var ManagerInterface
     */
    protected $themeManager;

    /**
     * @param ManagerInterface $themeManager
     */
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
    public function getSections(): array
    {
        $sections = [];

        $sections[] = $section = new FormRowSection('status', 'status', 'visibility', 'widgets');
        $section->setPriority(1000);
        $section->setGroup('sidebar');
        $section->setFields(['visibility']);

        $sections[] = $section = new Section('look', 'look', '@backend/widget/parts/look.tpl', 'widgets');
        $section->setPriority(800);
        $section->setGroup('sidebar');
        $section->setFields(['html_class', 'html_id', 'title', 'styles']);

        $sections[] = $section = new Section('widget', 'widgetOptions', '{% if widgetView %}
            {% include widgetView.views|first with widgetView.data|merge({form: form.widget_configuration}) %}
        {% endif %}', 'widgets');
        $section->setPriority(1000);
        $section->setFields('{% set fields = [] %}
            {% for key, item in form.widget_configuration %}
                {% set fields = fields|merge([key]) %}
            {% endfor %}');

        return $sections;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(object $object, string $scope): bool
    {
        return $object instanceof Widget && $scope === ScopeEnum::BACKEND_EDIT;
    }
}
