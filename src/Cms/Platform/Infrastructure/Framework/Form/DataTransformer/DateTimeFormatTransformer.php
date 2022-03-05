<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\DataTransformer;

use DateTime;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;

/**
 * @author Adam Banaszkiewicz
 */
class DateTimeFormatTransformer implements DataTransformerInterface
{
    protected $format;

    public function __construct(string $format)
    {
        $this->format = $format;
    }

    /**
     * @param string|DateTime $date
     * @return string|null
     * @throws TransformationFailedException
     */
    public function transform($date)
    {
        if ($date === null) {
            return null;
        }

        if ($date instanceof DateTime || $date instanceof ImmutableDateTime) {
            return $date->format($this->format);
        }

        if (is_string($date)) {
            return $date;
        }

        throw new TransformationFailedException(sprintf('Date must be string or DateTime object, given %s.', is_object($date) ? get_class($date) : gettype($date)));
    }

    /**
     * @param null|string $date
     * @return string|null
     * @throws TransformationFailedException
     */
    public function reverseTransform($date)
    {
        if ($date === null) {
            return null;
        }

        if (is_string($date)) {
            $formatted = DateTime::createFromFormat($this->format, $date);

            if ($formatted === false) {
                throw new TransformationFailedException(sprintf('Cannot transform simple date from format %s.', $this->format));
            }

            return $formatted->format($this->format);
        }

        throw new TransformationFailedException(sprintf('Date must be string or null, given %s.', is_object($date) ? get_class($date) : gettype($date)));
    }
}
