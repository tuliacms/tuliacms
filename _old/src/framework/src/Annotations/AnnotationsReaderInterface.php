<?php

declare(strict_types=1);

namespace Tulia\Framework\Annotations;

use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
interface AnnotationsReaderInterface
{
    public static function createFromRequest(Request $request): ?AnnotationsReaderInterface;
    public function getController(): string;
    public function getMethod(): string;
    public function getClassAnnotations(): array;
    public function getClassAnnotation(string $annotation): ?object;
    public function getMethodAnnotations(string $method = null): array;
    public function getMethodAnnotation(string $one, string $two = null): ?object;
    public function getPropertyAnnotations(string $property): array;
    public function getPropertyAnnotation(string $property, string $annotation): ?object;
}
