<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Translation\GoogleTranslationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class FieldTranslationController extends Controller
{
    public function __construct(
        protected GoogleTranslationService $translator
    ) {}

    /**
     * Translate a single field value.
     */
    public function translateField(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'text' => 'required|string|max:10000',
            'target' => 'nullable|string|in:ar,en',
            'source' => 'nullable|string|in:ar,en',
            'field' => 'nullable|string|max:100',
        ]);

        try {
            $text = $validated['text'];
            $target = $validated['target'] ?? null;
            $source = $validated['source'] ?? null;

            if ($target !== null) {
                $translated = $this->translator->translate($text, $target, $source);
                $detectedSource = $source ?? $this->translator->detectLanguage($text);
                $finalTarget = $target;
            } else {
                $result = $this->translator->autoTranslate($text);
                $translated = $result['translated'];
                $detectedSource = $result['source'];
                $finalTarget = $result['target'];
            }

            return response()->json([
                'success' => true,
                'field' => $validated['field'] ?? null,
                'original' => $text,
                'translated' => $translated,
                'source' => $detectedSource,
                'target' => $finalTarget,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Translation failed: ' . $e->getMessage(),
                'original' => $request->input('text'),
                'translated' => $request->input('text'),
            ], 500);
        }
    }

    /**
     * Translate multiple fields in batch.
     */
    public function translateBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fields' => 'required|array',
            'fields.*' => 'nullable|string|max:10000',
            'target' => 'nullable|string|in:ar,en',
        ]);

        try {
            $target = $validated['target'] ?? null;
            $results = [];

            foreach ($validated['fields'] as $key => $value) {
                if (empty(trim((string) $value))) {
                    $results[$key] = [
                        'original' => $value,
                        'translated' => $value,
                    ];
                    continue;
                }

                if ($target) {
                    $translated = $this->translator->translate($value, $target);
                    $results[$key] = [
                        'original' => $value,
                        'translated' => $translated,
                        'target' => $target,
                    ];
                } else {
                    $results[$key] = $this->translator->autoTranslate($value);
                }
            }

            return response()->json([
                'success' => true,
                'results' => $results,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Batch translation failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
