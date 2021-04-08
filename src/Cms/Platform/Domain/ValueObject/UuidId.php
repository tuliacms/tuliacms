<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Domain\ValueObject;

/**
 * @author Adam Banaszkiewicz
 */
abstract class UuidId
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $id
     */
    public function __construct(string $id)
    {
        if (preg_match('/[0-9a-f]{12}4[0-9a-f]{3}[89ab][0-9a-f]{15}/i', $id) === false) {
            throw new \InvalidArgumentException('AggregateId is not a valid UUID4.');
        }

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param UuidId $compare
     *
     * @return bool
     */
    public function equals(self $compare): bool
    {
        return $this->id === $compare->id;
    }
}
