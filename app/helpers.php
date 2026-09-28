<?php

use App\Services\Translation\GoogleTranslationService;

if (!function_exists('google_translate')) {
    /**
     * Translate text between Arabic and English using Google Translate.
     *
     * @param string $text
     * @param string|null $target
     * @param string|null $source
     * @return string
     */
    function google_translate(string $text, ?string $target = null, ?string $source = null): string
    {
        $service = app(GoogleTranslationService::class);
        
        if ($target === null) {
            $result = $service->autoTranslate($text);
            return $result['translated'];
        }

        return $service->translate($text, $target, $source);
    }
}

if (!function_exists('auto_translate_field')) {
    /**
     * Auto translate a specific field value and return details.
     *
     * @param string $fieldName
     * @param string $value
     * @param string|null $target
     * @return array
     */
    function auto_translate_field(string $fieldName, string $value, ?string $target = null): array
    {
        return app(GoogleTranslationService::class)->translateField($fieldName, $value, $target);
    }
}
