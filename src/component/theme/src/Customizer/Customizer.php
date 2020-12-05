<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer;

use Tulia\Component\Theme\Customizer\Builder\Controls\ControlInterface;
use Tulia\Component\Theme\Customizer\Builder\Section\Section;
use Tulia\Component\Theme\Customizer\Builder\Section\SectionInterface;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Customizer\Changeset\Factory\ChangesetFactoryInterface;
use Tulia\Component\Theme\Customizer\Builder\ThemeBuilderFactoryInterface;
use Tulia\Component\Theme\Customizer\Changeset\Transformer\ChangesetFieldsDefinitionControlsTransformer;
use Tulia\Component\Theme\Customizer\Provider\ProviderInterface;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Customizer implements CustomizerInterface
{
    /**
     * @var ChangesetFactoryInterface
     */
    protected $changesetFactory;

    /**
     * @var ThemeBuilderFactoryInterface
     */
    protected $builderFactory;

    /**
     * @var iterable|array
     */
    protected $providers;

    /**
     * @var array
     */
    protected $sections = [];

    /**
     * @var array
     */
    protected $controls = [];

    /**
     * @var bool
     */
    protected $fetched = false;

    /**
     * @param ChangesetFactoryInterface $changesetFactory
     * @param ThemeBuilderFactoryInterface $builderFactory
     * @param iterable|array $providers
     */
    public function __construct(
        ChangesetFactoryInterface $changesetFactory,
        ThemeBuilderFactoryInterface $builderFactory,
        iterable $providers
    ) {
        $this->changesetFactory = $changesetFactory;
        $this->builderFactory = $builderFactory;
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function addProvider(ProviderInterface $provider): void
    {
        $this->providers[] = $provider;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function addSection(string $id, array $params = []): SectionInterface
    {
        $section = new Section($id, $params);

        return $this->sections[$id] = $section;
    }

    /**
     * {@inheritdoc}
     */
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
            'translation_domain' => false,
        ], $params);

        $params['id'] = $id;
        $params['type'] = $type;

        $this->controls[$id] = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(): iterable
    {
        $this->fetchProviders();

        usort($this->sections, function ($a, $b) {
            return $b->get('priority') - $a->get('priority');
        });

        return $this->sections;
    }

    /**
     * {@inheritdoc}
     */
    public function getSection(string $id): ?SectionInterface
    {
        $this->fetchProviders();

        return $this->sections[$id] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getControls(): array
    {
        $this->fetchProviders();

        usort($this->controls, function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });

        return $this->controls;
    }

    /**
     * {@inheritdoc}
     */
    public function getControl(string $id): ?ControlInterface
    {
        $this->fetchProviders();

        return $this->controls[$id] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function fillChangesetWithDefaults(ChangesetInterface $changeset): void
    {
        foreach ($this->getControls() as $control) {
            $changeset->set($control['id'], $control['value']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildDefaultChangeset(ThemeInterface $theme): ChangesetInterface
    {
        $changeset = $this->changesetFactory->factory();
        $changeset->setTheme($theme->getName());

        $this->fillChangesetWithDefaults($changeset);

        return $changeset;
    }

    /**
     * {@inheritdoc}
     */
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
