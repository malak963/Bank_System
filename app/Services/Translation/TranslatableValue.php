<?php

namespace App\Services\Translation;

use ArrayAccess;
use JsonSerializable;
use Stringable;

class TranslatableValue implements Stringable, ArrayAccess, JsonSerializable
{
    /**
     * @param array<string, string> $translations
     */
    public function __construct(
        protected array $translations = []
    ) {}

    /**
     * Get the translated string for a given locale (defaults to current app locale).
     */
    public function get(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        if (!empty($this->translations[$locale])) {
            return (string) $this->translations[$locale];
        }

        if (!empty($this->translations[$fallback])) {
            return (string) $this->translations[$fallback];
        }

        foreach ($this->translations as $val) {
            if (!empty($val)) {
                return (string) $val;
            }
        }

        return '';
    }

    /**
     * Get the Arabic translation directly.
     */
    public function ar(): string
    {
        return (string) ($this->translations['ar'] ?? '');
    }

    /**
     * Get the English translation directly.
     */
    public function en(): string
    {
        return (string) ($this->translations['en'] ?? '');
    }

    /**
     * Convert to string for Blade {{ $model->field }} rendering.
     */
    public function __toString(): string
    {
        return $this->get();
    }

    /**
     * Return all translations as an array.
     */
    public function toArray(): array
    {
        return $this->translations;
    }

    public function jsonSerialize(): mixed
    {
        return $this->translations;
    }

    // ArrayAccess implementation
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->translations[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->translations[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->translations[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->translations[$offset]);
    }

    // Property access: $value->ar or $value->en
    public function __get(string $name)
    {
        return $this->translations[$name] ?? null;
    }

    public function __set(string $name, $value)
    {
        $this->translations[$name] = $value;
    }
}
