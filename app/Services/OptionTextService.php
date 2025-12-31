<?php

namespace App\Services;

use App\Models\Option;

class OptionTextService
{
    /**
     * Clean HTML from option text and handle image-only options
     */
    public static function cleanOptionText($text): string
    {
        if (empty($text)) {
            return '';
        }

        // Check if the text contains an HTML img tag
        if (preg_match('/<img[^>]*>/i', $text)) {
            // Remove HTML img tags
            $cleanText = preg_replace('/<img[^>]*>/i', '', $text);
            $cleanText = trim(strip_tags($cleanText));
            
            // If text is empty after removing images, this is an image-only option
            return $cleanText ?: '';
        }

        // Return regular text as-is
        return trim(strip_tags($text));
    }

    /**
     * Process option data for API responses
     */
    public static function processOptionForApi(Option $option, string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        $optionText = $option->getTranslation('option_text', $locale);
        $cleanText = self::cleanOptionText($optionText);
        
        // Check if there's an image in the HTML and extract it
        $imageUrl = $option->image_url ? asset($option->image_url) : null;
        
        // If no image_url but text contains HTML img, extract it
        if (!$imageUrl && preg_match('/<img[^>]*src=[\"\']?([^\s\"\'>]+)/i', $optionText, $matches)) {
            $imgPath = $matches[1];
            // Convert relative path to absolute if needed
            if (str_starts_with($imgPath, '../examMedia/')) {
                $imgPath = str_replace('../', '', $imgPath);
                $imageUrl = asset($imgPath);
            }
        }
        
        // If there's an image but no text, provide fallback text
        $displayText = $cleanText;
        if ($imageUrl && empty($cleanText)) {
            $displayText = "Option " . ($option->order + 1);
        }
        
        return [
            'id' => $option->id,
            'text' => $displayText,
            'image_url' => $imageUrl,
            'is_correct' => (bool)$option->is_correct,
            'explanation' => $option->getTranslation('explanation', $locale),
            'order' => $option->order
        ];
    }

    /**
     * Extract image URL from HTML string
     */
    public static function extractImageUrlFromHtml($html): ?string
    {
        if (preg_match('/<img[^>]*src=[\"\']?([^\s\"\'>]+)/i', $html, $matches)) {
            $imgPath = $matches[1];
            $imageName = basename(str_replace('../', '', $imgPath));
            
            // Only process .jpg files from examMedia directory
            if (str_ends_with(strtolower($imageName), '.jpg') && str_contains(strtolower($imgPath), 'exammedia/')) {
                return 'examMedia/' . $imageName;
            }
        }
        
        return null;
    }
}
