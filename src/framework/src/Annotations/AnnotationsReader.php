<?php

declare(strict_types=1);

namespace Tulia\Framework\Annotations;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class AnnotationsReader implements AnnotationsReaderInterface
{
    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var AnnotationReader|Reader
     */
    protected $reader;

    /**
     * @var ReflectionClass
     */
    protected $reflectionClass;

    /**
     * @param string $controller
     * @param string $method
     * @param Reader|null $reader
     */
    public function __construct(string $controller, string $method, Reader $reader = null)
    {
        $this->controller = $controller;
        $this->method     = $method;
        $this->reader     = $reader ?? new AnnotationReader();
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromRequest(Request $request): ?AnnotationsReaderInterface
    {
        $controller = $request->attributes->get('_controller');

        if (empty($controller) || \is_string($controller) === false) {
            return null;
        }

        return new self(...explode('::', $controller));
    }

    /**
     * {@inheritdoc}
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassAnnotations(): array
    {
        return $this->reader->getClassAnnotations($this->getReflectionClass());
    }

    /**
     * {@inheritdoc}
     */
    public function getClassAnnotation(string $annotation): ?object
    {
        return $this->reader->getClassAnnotation($this->getReflectionClass(), $annotation);
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodAnnotations(string $method = null): array
    {
        return $this->reader->getMethodAnnotations($this->getReflectionClass()->getMethod($method ?? $this->method));
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodAnnotation(string $one, string $two = null): ?object
    {
        if ($two === null) {
            $method     = $this->method;
            $annotation = $one;
        } else {
            $method     = $one;
            $annotation = $two;
        }

        return $this->reader->getMethodAnnotation(
            $this->getReflectionClass()->getMethod($method),
            $annotation
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyAnnotations(string $property): array
    {
        return $this->reader->getPropertyAnnotations($this->getReflectionClass()->getProperty($property));
    }

    /**
     * {@inheritdoc}
     */
    public function getPropertyAnnotation(string $property, string $annotation): ?object
    {
        return $this->reader->getPropertyAnnotation(
            $this->getReflectionClass()->getProperty($property),
            $annotation
        );
    }

    private function getReflectionClass(): ReflectionClass
    {
        if ($this->reflectionClass) {
            return $this->reflectionClass;
        }

        return $this->reflectionClass = new ReflectionClass($this->controller);
    }
}
