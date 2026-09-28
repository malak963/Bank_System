<?php

namespace App\Casts;

use App\Services\Translation\GoogleTranslationService;
use App\Services\Translation\TranslatableValue;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class TranslatableJson implements CastsAttributes
{
    /**
     * Cast the given value from the database.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return TranslatableValue|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?TranslatableValue
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return new TranslatableValue($value);
        }

        if ($value instanceof TranslatableValue) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return new TranslatableValue($decoded);
        }

        // If it was stored as a legacy plain string
        $text = (string) $value;
        $isAr = (bool) preg_match('/\p{Arabic}/u', $text);
        $translations = $isAr ? ['ar' => $text, 'en' => $text] : ['en' => $text, 'ar' => $text];

        return new TranslatableValue($translations);
    }

    /**
     * Prepare the given value for storage in the database.
     * Automatically translates missing Arabic or English translations using Google Translate.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof TranslatableValue) {
            $value = $value->toArray();
        }

        $service = app(GoogleTranslationService::class);
        $translations = [];

        if (is_string($value)) {
            $trimmed = trim($value);

            // Check if it's already a JSON string containing translations
            if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
                $decoded = json_decode($trimmed, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $translations = $decoded;
                }
            }

            // If not JSON, it's a raw string in either Arabic or English
            if (empty($translations)) {
                $auto = $service->autoTranslate($trimmed);
                if ($auto['source'] === 'ar') {
                    $translations = [
                        'ar' => $auto['original'],
                        'en' => $auto['translated'],
                    ];
                } else {
                    $translations = [
                        'en' => $auto['original'],
                        'ar' => $auto['translated'],
                    ];
                }
            }
        } elseif (is_array($value)) {
            $translations = $value;
        }

        // Ensure both 'ar' and 'en' exist by auto-translating the counterpart if one is missing
        $ar = trim((string) ($translations['ar'] ?? ''));
        $en = trim((string) ($translations['en'] ?? ''));

        if ($ar !== '' && $en === '') {
            $translations['en'] = $service->translate($ar, 'en', 'ar');
        } elseif ($en !== '' && $ar === '') {
            $translations['ar'] = $service->translate($en, 'ar', 'en');
        } elseif ($ar === '' && $en === '') {
            return null;
        }

        return json_encode($translations, JSON_UNESCAPED_UNICODE);
    }
}
