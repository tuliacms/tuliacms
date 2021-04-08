<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Parser;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements RegistryInterface
{
    /**
     * @var array|FieldParserInterface[]
     */
    private $parsers = [];

    /**
     * @var iterable
     */
    private $sourceParsers;

    public function __construct(iterable $sourceParsers)
    {
        $this->sourceParsers = $sourceParsers;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $type): FieldParserInterface
    {
        $this->prepareParsers();

        return $this->parsers[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $type): bool
    {
        $this->prepareParsers();

        return isset($this->parsers[$type]);
    }

    /**
     * {@inheritdoc}
     */
    public function add(FieldParserInterface $type): void
    {
        $this->parsers[\get_class($type)] = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        $this->prepareParsers();

        return $this->parsers;
    }

    protected function prepareParsers(): void
    {
        if ($this->parsers !== []) {
            return;
        }

        foreach ($this->sourceParsers as $parser) {
            $this->add($parser);
        }
    }
}
