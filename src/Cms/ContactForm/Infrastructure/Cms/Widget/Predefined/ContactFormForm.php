<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Cms\Widget\Predefined;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContactForm\Ports\Domain\ReadModel\ContactFormFinderScopeEnum;
use Tulia\Cms\ContactForm\Ports\Domain\ReadModel\ContactFormFinderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormForm extends AbstractType
{
    protected ContactFormFinderInterface $finder;

    public function __construct(ContactFormFinderInterface $finder)
    {
        $this->finder = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $result = $this->finder->find([], ContactFormFinderScopeEnum::INTERNAL);

        $forms = [];

        foreach ($result->all() as $form) {
            $forms[$form->getName()] = $form->getId();
        }

        $builder
            ->add('form_id', Type\ChoiceType::class, [
                'label' => 'form',
                'translation_domain' => 'forms',
                'constraints' => [
                    new Assert\Uuid(),
                    new Assert\NotBlank(),
                    new Assert\Choice([ 'choices' => $forms ]),
                ],
                'choices' => $forms,
                'choice_translation_domain' => false,
            ])
        ;
    }
}
