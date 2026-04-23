<?php

namespace App\Support;

use App\Models\PromptTemplate;
use App\Models\PromptTemplateVersion;

class PromptComparison
{
    /**
     * @return array{
     *     from: array{label:string,timestamp:mixed,fields:array<string, mixed>},
     *     to: array{label:string,timestamp:mixed,fields:array<string, mixed>},
     *     fields: array<int, array{key:string,label:string,changed:bool,from:mixed,to:mixed}>
     * }
     */
    public function build(PromptTemplate $promptTemplate, ?PromptTemplateVersion $fromVersion, ?PromptTemplateVersion $toVersion): array
    {
        $fromSnapshot = $this->makeSnapshot($promptTemplate, $fromVersion, 'From');
        $toSnapshot = $this->makeSnapshot($promptTemplate, $toVersion, 'To');

        $fields = [
            'title' => 'Title',
            'module' => 'Module',
            'description' => 'Description',
            'system_prompt' => 'System Prompt',
            'user_prompt_template' => 'User Prompt Template',
        ];

        return [
            'from' => $fromSnapshot,
            'to' => $toSnapshot,
            'fields' => collect($fields)->map(function (string $label, string $key) use ($fromSnapshot, $toSnapshot): array {
                $fromValue = $fromSnapshot['fields'][$key] ?? null;
                $toValue = $toSnapshot['fields'][$key] ?? null;

                return [
                    'key' => $key,
                    'label' => $label,
                    'changed' => $fromValue !== $toValue,
                    'from' => $fromValue,
                    'to' => $toValue,
                ];
            })->values()->all(),
        ];
    }

    /**
     * @return array{label:string,timestamp:mixed,fields:array<string, mixed>}
     */
    private function makeSnapshot(PromptTemplate $promptTemplate, ?PromptTemplateVersion $version, string $fallbackLabel): array
    {
        if ($version !== null) {
            return [
                'label' => 'Snapshot v' . $version->version_number,
                'timestamp' => $version->created_at ?? $version->updated_at,
                'fields' => [
                    'title' => $version->title,
                    'module' => $version->module,
                    'description' => $version->description,
                    'system_prompt' => $version->system_prompt,
                    'user_prompt_template' => $version->user_prompt_template,
                ],
            ];
        }

        return [
            'label' => $fallbackLabel === 'To' ? 'Current Live Template' : 'Current Live Template',
            'timestamp' => $promptTemplate->updated_at,
            'fields' => [
                'title' => $promptTemplate->title,
                'module' => $promptTemplate->module,
                'description' => $promptTemplate->description,
                'system_prompt' => $promptTemplate->system_prompt,
                'user_prompt_template' => $promptTemplate->user_prompt_template,
            ],
        ];
    }
}
