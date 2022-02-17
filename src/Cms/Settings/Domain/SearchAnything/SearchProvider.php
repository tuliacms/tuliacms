<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\SearchAnything;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Options\Domain\WriteModel\Exception\OptionNotFoundException;
use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Cms\SearchAnything\Domain\Model\Hit;
use Tulia\Cms\SearchAnything\Domain\Model\Results;
use Tulia\Cms\SearchAnything\Ports\Provider\AbstractProvider;
use Tulia\Cms\Settings\Ports\Domain\Group\SettingsGroupRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SearchProvider extends AbstractProvider
{
    protected SettingsGroupRegistryInterface $settings;

    protected FormFactoryInterface $formFactory;

    protected OptionsRepositoryInterface $options;

    protected TranslatorInterface $translator;

    protected RouterInterface $router;

    public function __construct(
        SettingsGroupRegistryInterface $settings,
        FormFactoryInterface $formFactory,
        OptionsRepositoryInterface $options,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->settings = $settings;
        $this->formFactory = $formFactory;
        $this->options = $options;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function search(string $query, int $limit = 5, int $page = 1): Results
    {
        $results = new Results();

        foreach ($this->settings->all() as $group => $groupObj) {
            $groupObj->setFormFactory($this->formFactory);
            $groupObj->setOptions($this->options);

            try {
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
                        $results->add(
                            $group,
                            new Hit($label, $this->router->generate('backend.settings', ['group' => $group]))
                        );
                        $limit--;
                    }
                }

                if ($limit === 0) {
                    break;
                }
            } catch (OptionNotFoundException $e) {
                // When Option not found we don't want to fail other settings groups
                // so we catch the error and then search other groups as well.
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
