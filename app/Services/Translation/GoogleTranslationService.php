<?php

namespace App\Services\Translation;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;

class GoogleTranslationService
{
    /**
     * HTTP client options for GoogleTranslate (disable SSL verification on local/windows environments).
     */
    protected array $options = [
        'verify' => false,
        'timeout' => 10.0,
    ];

    /**
     * In-memory cache for the current request / process life-cycle.
     */
    protected static array $runtimeCache = [];

    /**
     * Detect whether the text contains Arabic characters.
     */
    public function isArabic(string $text): bool
    {
        return (bool) preg_match('/\p{Arabic}/u', $text);
    }

    /**
     * Detect language: returns 'ar' if Arabic characters are present, else 'en'.
     */
    public function detectLanguage(string $text): string
    {
        return $this->isArabic($text) ? 'ar' : 'en';
    }

    /**
     * Translate text from source to target language.
     *
     * @param string $text The text to translate
     * @param string $target Target language ('ar' or 'en')
     * @param string|null $source Source language ('ar', 'en', or null for auto-detection)
     * @return string
     */
    public function translate(string $text, string $target = 'en', ?string $source = null): string
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return $text;
        }

        // If source is not specified, determine source or auto-detect
        if ($source === null) {
            $source = $this->detectLanguage($trimmed);
        }

        // If source and target are the same, no translation needed
        if ($source === $target) {
            return $text;
        }

        $cacheKey = $source . '_' . $target . '_' . md5($trimmed);

        // Check runtime memory cache first
        if (isset(static::$runtimeCache[$cacheKey])) {
            return static::$runtimeCache[$cacheKey];
        }

        // Attempt Laravel Cache (with graceful fallback if DB/Cache store is temporarily down)
        try {
            $translated = Cache::remember('gtrans_' . $cacheKey, now()->addDays(30), function () use ($trimmed, $target, $source, $text) {
                return $this->performTranslation($trimmed, $target, $source) ?: $text;
            });

            static::$runtimeCache[$cacheKey] = $translated;
            return $translated;
        } catch (Throwable $e) {
            // Cache failed (e.g. database down), execute directly
            $translated = $this->performTranslation($trimmed, $target, $source) ?: $text;
            static::$runtimeCache[$cacheKey] = $translated;
            return $translated;
        }
    }

    /**
     * Execute translation using Stichoza GoogleTranslate.
     */
    protected function performTranslation(string $text, string $target, ?string $source): string
    {
        try {
            $tr = new GoogleTranslate($target, $source, $this->options);
            return $tr->translate($text) ?: $text;
        } catch (Throwable $e) {
            Log::warning('GoogleTranslate execution error: ' . $e->getMessage(), [
                'text' => $text,
                'source' => $source,
                'target' => $target,
            ]);
            return $text;
        }
    }

    /**
     * Auto-translate text between Arabic and English based on detected language.
     * If input is Arabic, translates to English.
     * If input is English, translates to Arabic.
     *
     * @param string $text
     * @param string|null $forcedTarget
     * @return array{original: string, translated: string, source: string, target: string}
     */
    public function autoTranslate(string $text, ?string $forcedTarget = null): array
    {
        $trimmed = trim($text);
        if ($trimmed === '') {
            return [
                'original' => $text,
                'translated' => '',
                'source' => 'en',
                'target' => 'ar',
            ];
        }

        $detectedSource = $this->detectLanguage($trimmed);
        $target = $forcedTarget ?? ($detectedSource === 'ar' ? 'en' : 'ar');

        $translated = $this->translate($trimmed, $target, $detectedSource);

        return [
            'original' => $trimmed,
            'translated' => $translated,
            'source' => $detectedSource,
            'target' => $target,
        ];
    }

    /**
     * Translate a specific field and return metadata.
     */
    public function translateField(string $fieldName, string $value, ?string $target = null, ?string $source = null): array
    {
        $result = $this->autoTranslate($value, $target);
        $result['field'] = $fieldName;

        return $result;
    }

    /**
     * Translate an array of fields in a model or dataset.
     */
    public function translateFields(array $data, array $fields, string $target = 'en'): array
    {
        $translatedData = $data;

        foreach ($fields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $translatedData[$field] = $this->translate($data[$field], $target);
            }
        }

        return $translatedData;
    }
}
