<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Twig\Extension;

use Tulia\Component\FormBuilder\Builder\BuilderInterface;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class FormExtension extends AbstractExtension
{
    /**
     * @var BuilderInterface
     */
    protected $builder;

    /**
     * @param BuilderInterface $builder
     */
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
            new TwigFunction('form_extension_render', function (Environment $environment, $context, ManagerInterface $manager, ?string $group = null, array $options = []) {
                $template = $environment->createTemplate($this->builder->build($manager, $group, $options));
                return $environment->render($template, $context);
            }, [
                'is_safe' => [ 'html' ],
                'needs_context' => true,
                'needs_environment' => true,
            ]),
        ];
    }
}
