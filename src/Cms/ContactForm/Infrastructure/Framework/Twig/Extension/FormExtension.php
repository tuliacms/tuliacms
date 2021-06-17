<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContactForm\Domain\ReadModel\Finder\Model\Form;
use Tulia\Cms\ContactForm\Ports\Domain\ReadModel\ContactFormFinderInterface;
use Tulia\Cms\ContactForm\Ports\UserInterface\Web\Frontend\FormBuilder\ContactFormBuilderInterface;
use Tulia\Cms\ContactForm\Ports\Domain\ReadModel\ContactFormFinderScopeEnum;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class FormExtension extends AbstractExtension
{
    private ContactFormBuilderInterface $builder;

    private ContactFormFinderInterface $finder;

    public function __construct(ContactFormBuilderInterface $builder, ContactFormFinderInterface $finder)
    {
        $this->builder = $builder;
        $this->finder = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('contact_form', function (Environment $env, $context, string $formId) {
                $model = $this->finder->findOne(['id' => $formId, 'fetch_fields' => true], ContactFormFinderScopeEnum::SINGLE);

                if ($model === null) {
                    return null;
                }

                return $env->render('@cms/forms/contact-form.tpl', [
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
                $model = $this->finderFactory->find($formId, ContactFormFinderScopeEnum::SINGLE);

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
