<?php

namespace App\Traits;

use App\Services\Translation\GoogleTranslationService;

trait AutoTranslatable
{
    /**
     * Get the translatable attributes for the model.
     *
     * @return array
     */
    public function getAutoTranslatableAttributes(): array
    {
        return property_exists($this, 'autoTranslatable') ? $this->autoTranslatable : [];
    }

    /**
     * Translate a specific attribute of the model to a target language.
     *
     * @param string $attribute
     * @param string $targetLocale
     * @return string
     */
    public function translateAttribute(string $attribute, string $targetLocale = 'en'): string
    {
        $value = (string) $this->getAttribute($attribute);
        if (trim($value) === '') {
            return '';
        }

        return app(GoogleTranslationService::class)->translate($value, $targetLocale);
    }

    /**
     * Automatically translate all configured translatable attributes and return them as an array.
     *
     * @param string|null $targetLocale
     * @return array
     */
    public function autoTranslateAll(?string $targetLocale = null): array
    {
        $service = app(GoogleTranslationService::class);
        $results = [];

        foreach ($this->getAutoTranslatableAttributes() as $attribute) {
            $value = (string) $this->getAttribute($attribute);
            if ($value !== '') {
                $results[$attribute] = $service->autoTranslate($value, $targetLocale);
            }
        }

        return $results;
    }
}
