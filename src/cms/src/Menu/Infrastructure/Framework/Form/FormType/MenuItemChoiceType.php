<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\MultipleFetchException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryException;
use Tulia\Cms\Menu\Application\Query\Finder\Exception\QueryNotFetchedException;
use Tulia\Cms\Menu\Application\Query\Finder\Enum\ScopeEnum;
use Tulia\Cms\Menu\Application\Query\Finder\FinderFactoryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuItemChoiceType extends ChoiceType
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param FinderFactoryInterface $finderFactory
     */
    public function __construct(FinderFactoryInterface $finderFactory, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->finderFactory = $finderFactory;
        $this->translator    = $translator;
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

    /**
     * @param string $menuId
     *
     * @return array
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    protected function collectChoices(string $menuId): array
    {
        $source = $this->finderFactory->getInstance(ScopeEnum::INTERNAL)->find($menuId, ['visibility' => null]);

        if (! $source) {
            return [];
        }

        $choices = [];

        foreach ($source->getItems() as $item) {
            $name = $item->getName();

            if ($item->getLevel()) {
                $name = str_repeat('- ', $item->getLevel()) . $name;
            }

            $choices[$item->getId()] = $name;
        }

        return $choices;
    }
}
