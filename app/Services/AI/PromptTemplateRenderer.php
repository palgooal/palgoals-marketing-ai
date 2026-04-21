<?php

namespace App\Services\AI;

use App\Models\PromptTemplate;

class PromptTemplateRenderer
{
    /**
     * @param  array<string, mixed>  $context
     * @return array{system_prompt: string|null, user_prompt: string}
     */
    public function render(PromptTemplate $promptTemplate, array $context): array
    {
        $replacements = $this->buildReplacementMap($context);

        $systemPrompt = $promptTemplate->system_prompt !== null
            ? $this->replacePlaceholders($promptTemplate->system_prompt, $replacements)
            : null;

        $userPrompt = $this->replacePlaceholders($promptTemplate->user_prompt_template, $replacements);
        $inputPayload = $context['input_payload'] ?? [];

        if (is_array($inputPayload) && $inputPayload !== []) {
            $userPrompt .= PHP_EOL.PHP_EOL.'Input payload:'.PHP_EOL
                .json_encode($inputPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return [
            'system_prompt' => $systemPrompt,
            'user_prompt' => $userPrompt,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, string>
     */
    private function buildReplacementMap(array $context): array
    {
        $replacements = [];

        foreach (['title', 'type', 'language', 'tone', 'context'] as $key) {
            $replacements["{{{$key}}}"] = $this->stringValue($context[$key] ?? '');
        }

        foreach (($context['input_payload'] ?? []) as $key => $value) {
            if (is_string($key)) {
                $replacements["{{{$key}}}"] = $this->stringValue($value);
            }
        }

        return $replacements;
    }

    /**
     * @param  array<string, string>  $replacements
     */
    private function replacePlaceholders(string $text, array $replacements): string
    {
        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $text,
        );
    }

    private function stringValue(mixed $value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return '';
        }

        return (string) $value;
    }
}
