<?php

namespace App\Traits;

use App\Casts\TranslatableJson;
use App\Services\Translation\TranslatableValue;

trait HasAutoTranslations
{
    /**
     * Initialize the trait and automatically bind TranslatableJson cast to translatable fields.
     */
    public function initializeHasAutoTranslations(): void
    {
        $casts = [];
        foreach ($this->getTranslatableAttributes() as $attribute) {
            $casts[$attribute] = TranslatableJson::class;
        }

        if (!empty($casts)) {
            $this->mergeCasts($casts);
        }
    }

    /**
     * Get the list of translatable attributes for this model.
     *
     * @return array<string>
     */
    public function getTranslatableAttributes(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : [];
    }

    /**
     * Check if an attribute is translatable.
     */
    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->getTranslatableAttributes(), true);
    }

    /**
     * Get the translation for a given attribute and locale.
     */
    public function getTranslation(string $attribute, ?string $locale = null): string
    {
        $val = $this->getAttribute($attribute);

        if ($val instanceof TranslatableValue) {
            return $val->get($locale);
        }

        if (is_array($val)) {
            $loc = $locale ?? app()->getLocale();
            return (string) ($val[$loc] ?? reset($val) ?? '');
        }

        return (string) $val;
    }

    /**
     * Get all translations for a given attribute.
     */
    public function getTranslations(string $attribute): array
    {
        $val = $this->getAttribute($attribute);

        if ($val instanceof TranslatableValue) {
            return $val->toArray();
        }

        if (is_array($val)) {
            return $val;
        }

        return [];
    }

    /**
     * Explicitly set a translation for a specific locale.
     */
    public function setTranslation(string $attribute, string $locale, string $value): self
    {
        $translations = $this->getTranslations($attribute);
        $translations[$locale] = $value;

        $this->setAttribute($attribute, $translations);

        return $this;
    }
}
