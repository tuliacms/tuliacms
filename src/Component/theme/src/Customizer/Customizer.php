<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer;

use Tulia\Component\Theme\Customizer\Builder\Controls\ControlInterface;
use Tulia\Component\Theme\Customizer\Builder\Section\SectionInterface;
use Tulia\Component\Theme\Customizer\Builder\Section\SectionsFactoryInterface;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\Customizer\Changeset\Transformer\ChangesetFieldsDefinitionControlsTransformer;
use Tulia\Component\Theme\Customizer\Provider\ProviderInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Customizer implements CustomizerInterface
{
    protected ChangesetFactoryInterface $changesetFactory;
    protected SectionsFactoryInterface $sectionsFactory;
    protected iterable $providers;
    protected array $sections = [];
    protected array $controls = [];
    protected bool $fetched = false;
    protected ?string $translationDomain = null;

    public function __construct(
        ChangesetFactoryInterface $changesetFactory,
        SectionsFactoryInterface $sectionsFactory,
        iterable $providers
    ) {
        $this->changesetFactory = $changesetFactory;
        $this->sectionsFactory = $sectionsFactory;
        $this->providers = $providers;
    }

    public function addProvider(ProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    public function getProviders(): iterable
    {
        return $this->providers;
    }

    /**
     * @param ChangesetInterface $changeset
     */
    public function configureFieldsTypes(ChangesetInterface $changeset): void
    {
        (new ChangesetFieldsDefinitionControlsTransformer())
            ->transform($changeset, $this->getControls());
    }

    public function setTranslationDomain(?string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function addSection(string $id, array $params = []): SectionInterface
    {
        $params = array_merge([
            'translation_domain' => $this->translationDomain,
        ], $params);

        return $this->sections[$id] = $this->sectionsFactory->create($id, $params);
    }

    public function addControl(string $id, string $type, array $params = []): void
    {
        $params = array_merge([
            'label'        => '',
            'help'         => null,
            'multilingual' => false,
            'value'        => null,
            'default'      => null,
            'transport'    => 'refresh',
            'section'      => null,
            'priority'     => 0,
            'input_attrs'  => [],
            'translation_domain' => $this->translationDomain,
        ], $params);

        $params['id'] = $id;
        $params['type'] = $type;

        $this->controls[$id] = $params;
    }

    public function getSections(): iterable
    {
        $this->fetchProviders();

        usort($this->sections, function ($a, $b) {
            return $b->get('priority') - $a->get('priority');
        });

        return $this->sections;
    }

    public function getSection(string $id): ?SectionInterface
    {
        $this->fetchProviders();

        return $this->sections[$id] ?? null;
    }

    public function getControls(): array
    {
        $this->fetchProviders();

        usort($this->controls, function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        return $this->controls;
    }

    public function getControl(string $id): ?ControlInterface
    {
        $this->fetchProviders();

        return $this->controls[$id] ?? null;
    }

    public function fillChangesetWithDefaults(ChangesetInterface $changeset): void
    {
        foreach ($this->getControls() as $control) {
            $changeset->set($control['id'], $control['value']);
        }
    }

    public function buildDefaultChangeset(ThemeInterface $theme): ChangesetInterface
    {
        $changeset = $this->changesetFactory->factory();
        $changeset->setTheme($theme->getName());

        $this->fillChangesetWithDefaults($changeset);

        return $changeset;
    }

    public function getPredefinedChangesets(): iterable
    {
        $predefined = [];

        foreach ($this->providers as $provider) {
            $predefined += $provider->getPredefined($this->changesetFactory);
        }

        return $predefined;
    }

    private function fetchProviders(): void
    {
        if ($this->fetched) {
            return;
        }

        foreach ($this->providers as $provider) {
            $provider->build($this);
        }

        $this->fetched = true;
    }
}
