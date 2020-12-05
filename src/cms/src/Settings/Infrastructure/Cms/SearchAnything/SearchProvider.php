<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Infrastructure\Cms\SearchAnything;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Options\OptionsInterface;
use Tulia\Cms\SearchAnything\Provider\AbstractProvider;
use Tulia\Cms\SearchAnything\Results\Hit;
use Tulia\Cms\SearchAnything\Results\Results;
use Tulia\Cms\SearchAnything\Results\ResultsInterface;
use Tulia\Cms\Settings\RegistryInterface;
use Tulia\Component\Routing\RouterInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    /**
     * @var RegistryInterface
     */
    protected $settings;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var OptionsInterface
     */
    protected $options;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param RegistryInterface $settings
     * @param FormFactoryInterface $formFactory
     * @param OptionsInterface $options
     * @param TranslatorInterface $translator
     * @param RouterInterface $router
     */
    public function __construct(
        RegistryInterface $settings,
        FormFactoryInterface $formFactory,
        OptionsInterface $options,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->settings = $settings;
        $this->formFactory = $formFactory;
        $this->options = $options;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function search(string $query, int $limit = 5, int $page = 1): ResultsInterface
    {
        $results = new Results();

        foreach ($this->settings->all() as $group => $groupObj) {
            $groupObj->setFormFactory($this->formFactory);
            $groupObj->setOptions($this->options);
            $form = $groupObj->buildForm();

            foreach ($form->all() as $field) {
                $label = $this->translator->trans(
                    $field->getConfig()->getOption('label'),
                    $field->getConfig()->getOption('label_translation_parameters'),
                    $field->getConfig()->getOption('translation_domain')
                );
                $labelLower = mb_strtolower($label);

                if (mb_strpos($labelLower, $query) !== false && $limit) {
                    $label = sprintf(
                        '%s - %s',
                        $this->translator->trans($groupObj->getName(), [], $groupObj->getTranslationDomain()),
                        $label
                    );
                    $results->add(new Hit($label, $this->router->generate('backend.settings', [ 'group' => $group ])));
                    $limit--;
                }
            }

            if ($limit === 0) {
                break;
            }
        }

        return $results;
    }

    public function getId(): string
    {
        return 'settings';
    }

    public function getLabel(): array
    {
        return ['settings'];
    }

    public function getIcon(): string
    {
        return 'fas fa-cogs';
    }
}
