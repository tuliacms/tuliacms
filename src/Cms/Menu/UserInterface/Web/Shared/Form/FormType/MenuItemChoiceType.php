<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UserInterface\Web\Shared\Form\FormType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderInterface;
use Tulia\Cms\Menu\Domain\ReadModel\Finder\MenuFinderScopeEnum;
use Tulia\Cms\Menu\Domain\WriteModel\Model\Item;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemChoiceType extends ChoiceType
{
    protected MenuFinderInterface $menuFinder;

    protected TranslatorInterface $translator;

    public function __construct(MenuFinderInterface $menuFinder, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->menuFinder = $menuFinder;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->collectChoices($options['menu_id']);

        $options['choices'] = array_flip($choices);
        $options['constraints'] = [
            new Assert\Choice([
                'choices'  => $choices,
                'multiple' => $options['multiple'],
            ]),
        ];

        if ($options['empty_option']) {
            $options['choices'] = array_merge(
                [
                    $this->translator->trans($options['empty_option_label'], [], $options['empty_option_translation_domain']) => ''
                ],
                $options['choices']
            );
        }

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            /**
             * Default taxonomy label.
             */
            'label' => 'menu',
            /**
             * Prevent translate choices.
             */
            'choice_translation_domain' => false,
            /**
             * Menu ID from items should be taken.
             */
            'menu_id' => null,
            /**
             * Option adds empty option to select, at the beginning.
             */
            'empty_option' => false,
            'empty_option_label' => 'selectBlankValue',
            'empty_option_translation_domain' => 'messages',
        ]);

        $resolver->setAllowedTypes('menu_id', 'string');
        $resolver->setAllowedTypes('empty_option', 'bool');
        $resolver->setAllowedTypes('empty_option_label', [ 'null', 'string' ]);
        $resolver->setAllowedTypes('empty_option_translation_domain', [ 'null', 'bool', 'string' ]);
    }

    protected function collectChoices(string $menuId): array
    {
        $source = $this->menuFinder->findOne(['id' => $menuId], MenuFinderScopeEnum::INTERNAL);

        if (! $source) {
            return [];
        }

        $items = $this->sort($source->getItems());

        $choices = [];

        foreach ($items as $item) {
            $name = $item->getName();

            if ($item->getLevel()) {
                $name = str_repeat('- ', $item->getLevel() - 1) . $name;
            }

            $choices[$item->getId()] = $name;
        }

        return $choices;
    }

    private function sort(array $items, int $level = 1, string $parent = Item::ROOT_ID): array
    {
        $result = [];

        foreach ($items as $item) {
            if ($item->getLevel() === $level && $item->getParentId() === $parent) {
                $result[] = [$item];
                $result[] = $this->sort($items, $level + 1, $item->getId());
            }
        }

        if ($result === []) {
            return [];
        }

        return array_merge(...$result);
    }
}
