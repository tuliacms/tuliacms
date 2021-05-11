<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Twig\Extension;

use Symfony\Component\Form\FormView;
use Tulia\Component\FormBuilder\Builder\BuilderInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class FormExtension extends AbstractExtension
{
    protected BuilderInterface $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('form_skeleton_render', function (Environment $environment, $context, FormView $form, ?string $group = null, array $options = []) {
                return $environment->render(
                    $environment->createTemplate(
                        $this->builder->build($form, $group, $options)
                    ),
                    $context
                );
            }, [
                'is_safe' => [ 'html' ],
                'needs_context' => true,
                'needs_environment' => true,
            ]),
        ];
    }
}
