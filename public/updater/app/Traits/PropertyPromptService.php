<?php

namespace App\Traits;

trait PropertyPromptService
{

    public function generatePropertyPrompt($language, $data, $fieldType = null)
    {
        $prompt = "You are an expert real estate content creator.\n\n";
        $prompt .= "Generate a compelling property listing in {$language} based on the following details:\n";
        $prompt .= "- **Property Type**: {$data['ai_property_type']}\n";
        $prompt .= "- **Listing Purpose**: For {$data['ai_purpose']}\n";
        $prompt .= "- **Category**: {$data['ai_category_name']}\n";
        $prompt .= "- **Country**: {$data['ai_country_name']}\n";
        $prompt .= "- **Area**: {$data['ai_area']} sqft\n";

        if (!empty($data['ai_amenities_names'])) {
            $prompt .= "- **Amenities**: " . implode(', ', $data['ai_amenities_names']) . "\n";
        }

        $prompt .= "- **Core Idea/Prompt**: {$data['ai_content_prompt']}\n\n";

        // Define all possible fields
        $allFields = [
            'title' => "A catchy and descriptive title for the property.",
            'description' => "A detailed, engaging, and professional description of the property. Make this the most detailed part, with a minimum of 300 words.",
            'meta_keyword' => "A comma-separated list of 10-15 relevant SEO keywords.",
            'meta_description' => "A concise and compelling meta description for search engines, around 155 characters."
        ];

        // Determine which fields to generate
        $fieldsToGenerate = $fieldType && isset($allFields[$fieldType])
            ? [$fieldType => $allFields[$fieldType]]
            : $allFields;

        $prompt .= "Based on this, generate the following content field(s):\n";

        $index = 1;
        foreach ($fieldsToGenerate as $key => $desc) {
            $prompt .= "{$index}. **{$key}**: {$desc}\n";
            $index++;
        }

        $prompt .= "\nOutput must be **valid JSON only**. Do not include any other text, markdown, or explanation outside of the JSON object. The JSON structure should be:\n";
        $prompt .= "{\n";

        $jsonLines = [];
        foreach ($fieldsToGenerate as $key => $desc) {
            $jsonLines[] = "  \"{$key}\": \"(generated " . str_replace('_', ' ', $key) . ")\"";
        }
        $prompt .= implode(",\n", $jsonLines) . "\n";
        $prompt .= "}\n";

        return $prompt;
    }
    public function generateProjectPrompt($language, $data, $fieldType = null)
    {
        $prompt = "You are an expert real estate copywriter and SEO-focused content creator.\n\n";
        $prompt .= "Generate a compelling property listing in {$language} based on the following details:\n";

        $prompt .= "- **Category**: {$data['ai_category_name']}\n";
        $prompt .= "- **Country**: {$data['ai_country_name']}\n";

        $prompt .= "- **Core Idea/Prompt**: {$data['ai_content_prompt']}\n\n";

        // Define all possible fields
        $allFields = [
            'title' => "A concise, keyword-rich property title (≤60 characters) that front-loads property type, location, and one standout feature; no emojis or ALL CAPS.",
            'description' => "A detailed, professional property description of at least 300 words, written for buyers and search intent, using short paragraphs and skimmable structure.",
            'meta_keyword' => "A comma-separated list of relevant SEO keywords focused on property type, location, and core features; avoid duplicates and stuffing.",
            'meta_description' => "A compelling meta description of 150–160 characters including the primary keyword, location, and a clear call to action."
        ];

        // Determine which fields to generate
        $fieldsToGenerate = $fieldType && isset($allFields[$fieldType])
            ? [$fieldType => $allFields[$fieldType]]
            : $allFields;

        $prompt .= "Based on this, generate the following content field(s):\n";

        $index = 1;
        foreach ($fieldsToGenerate as $key => $desc) {
            $prompt .= "{$index}. **{$key}**: {$desc}\n";
            $index++;
        }

        $prompt .= "\nOutput must be **valid JSON only**. Do not include any other text, markdown, or explanation outside of the JSON object. The JSON structure should be:\n";
        $prompt .= "{\n";

        $jsonLines = [];
        foreach ($fieldsToGenerate as $key => $desc) {
            $jsonLines[] = "  \"{$key}\": \"(generated " . str_replace('_', ' ', $key) . ")\"";
        }
        $prompt .= implode(",\n", $jsonLines) . "\n";
        $prompt .= "}\n";

        return $prompt;
    }
}
