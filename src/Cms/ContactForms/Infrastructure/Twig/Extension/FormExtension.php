<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Twig\Extension;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Tulia\Cms\ContactForms\Application\Builder\BuilderInterface;
use Tulia\Cms\ContactForms\Query\Enum\ScopeEnum;
use Tulia\Cms\ContactForms\Query\FinderFactoryInterface;
use Tulia\Cms\ContactForms\Query\Model\Form;
use Tulia\Framework\Http\Request;
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
    private $builder;

    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * @param BuilderInterface $builder
     */
    public function __construct(BuilderInterface $builder, FinderFactoryInterface $finderFactory)
    {
        $this->builder = $builder;
        $this->finderFactory = $finderFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('contact_form', function (Environment $env, $context, string $formId) {
                $model = $this->finderFactory->find($formId, ScopeEnum::SINGLE);

                if ($model === null) {
                    return null;
                }

                return $env->render('render_form', [
                    'template' => $model->getFieldsView(),
                    'template_name' => sprintf('contact_form_field_template_%s', $formId),
                    'form' => $this->buildForm(
                        $context['app']->getRequest(),
                        $model
                    ),
                ]);
            }, [
                'is_safe' => [ 'html' ],
                'needs_environment' => true,
                'needs_context' => true,
            ]),
            new TwigFunction('get_contact_form', function ($context, string $formId) {
                $model = $this->finderFactory->find($formId, ScopeEnum::SINGLE);

                if ($model === null) {
                    return null;
                }

                return $this->buildForm($context['app']->getRequest(), $model);
            }, [
                'is_safe' => [ 'html' ],
                'needs_context' => true,
            ]),
            /**
             * Return string of generated form fields with labels and submitted values.
             */
            new TwigFunction('contact_form_fields', function ($context) {
                return $context['__contact_form_fields'] ?? '';
            }, [
                'is_safe' => [ 'html' ],
                'needs_context' => true,
            ]),
            /**
             * Return only one submitted value, by given name.
             */
            new TwigFunction('contact_form_field', function ($context, string $field) {
                return $context['__contact_form_data'][$field] ?? '';
            }, [
                'is_safe' => [ 'html' ],
                'needs_context' => true,
            ]),
        ];
    }

    private function buildForm(Request $request, Form $model): ?FormView
    {
        $errors = $this->getFlashDataArray($request, 'cms.form.last_errors');
        $data   = $this->getFlashDataArray($request, 'cms.form.last_data');

        $form = $this->builder->build($model, $data);
        $form->handleRequest($request);

        foreach ($errors as $name => $messages) {
            $field = $form->get($name);

            foreach ($messages as $message) {
                $field->addError(new FormError($message));
            }
        }

        $view = $form->createView();

        foreach ($errors as $name => $messages) {
            $view->children[$name]->vars['valid'] = false;
        }

        return $view;
    }

    private function getFlashDataArray(Request $request, string $name): array
    {
        $data = $request->getSession()->getFlashBag()->get($name);

        return isset($data[0]) && \is_string($data[0]) ? (@ (array) json_decode($data[0], true)) : [];
    }
}
