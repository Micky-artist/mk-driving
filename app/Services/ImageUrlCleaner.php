<?php

namespace App\Services;

class ImageUrlCleaner
{
    /**
     * Clean image URL from HTML img tags or return null if invalid
     *
     * @param string|null $imageUrl
     * @return string|null
     */
    public static function clean(?string $imageUrl): ?string
    {
        if (empty($imageUrl)) {
            return null;
        }

        // If it's already a clean URL (no HTML tags), return as-is
        if (!self::containsHtmlTags($imageUrl)) {
            return self::validateAndCleanUrl($imageUrl);
        }

        // Extract URL from img tag
        $cleanUrl = self::extractImageUrlFromHtml($imageUrl);
        
        return self::validateAndCleanUrl($cleanUrl);
    }

    /**
     * Check if string contains HTML tags
     *
     * @param string $string
     * @return bool
     */
    private static function containsHtmlTags(string $string): bool
    {
        return $string !== strip_tags($string);
    }

    /**
     * Extract URL from img tag using regex
     *
     * @param string $imgTag
     * @return string|null
     */
    private static function extractUrlFromImgTag(string $imgTag): ?string
    {
        // Match src attribute in img tag (both quoted and unquoted)
        if (preg_match('/<img[^>]+src\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^>\s]+))[^>]*>/i', $imgTag, $matches)) {
            // Return the non-null match from the three capture groups
            return $matches[1] ?? $matches[2] ?? $matches[3] ?? null;
        }

        return null;
    }

    /**
     * Validate and clean URL - only clean HTML tags, don't prefix local paths
     *
     * @param string|null $url
     * @return string|null
     */
    private static function validateAndCleanUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Remove any remaining HTML entities or special characters
        $url = html_entity_decode($url);
        $url = trim($url);

        // Handle relative paths like "../examMedia/427.jpg"
        if (str_starts_with($url, '../')) {
            $url = 'examMedia/' . substr($url, 3);
        }

        // Fix double examMedia/ paths like "examMedia/examMedia/triangle"
        if (str_starts_with($url, 'examMedia/examMedia/')) {
            $url = substr($url, 9); // Remove the first "examMedia/"
        }

        // Ensure the URL starts with / for proper browser loading
        if (!str_starts_with($url, '/') && !str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = '/' . $url;
        }

        // Don't add any prefixes - just return the clean path
        // Local paths like "examMedia/427.jpg" should stay as-is
        // External URLs with http/https should stay as-is

        return $url;
    }

    /**
     * Extract image URL from HTML string
     *
     * @param string $html
     * @return string|null
     */
    public static function extractImageUrlFromHtml($html): ?string
    {
        if (empty($html)) {
            return null;
        }

        // Handle case where html might be an array (multilingual content)
        if (is_array($html)) {
            // Get the current locale's text, or fallback to first available
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale', 'en');
            
            $text = $html[$locale] ?? $html[$fallbackLocale] ?? array_values($html)[0] ?? null;
            if ($text) {
                return self::extractImageUrlFromHtml($text);
            }
            return null;
        }

        // Simple string parsing to extract src attribute value
        $srcPos = strpos(strtolower($html), 'src=');
        if ($srcPos !== false) {
            // Find where alt= starts (if it exists)
            $altPos = stripos($html, 'alt=');
            
            // Start after src=
            $startPos = $srcPos + 4;
            
            // Skip any whitespace after src=
            while ($startPos < strlen($html) && in_array($html[$startPos], [' ', '"', "'"])) {
                $startPos++;
            }
            
            // End position is either before alt= or end of string
            if ($altPos !== false && $altPos > $startPos) {
                $endPos = $altPos - 1; // Go back to the space before alt=
                // Trim trailing spaces
                while ($endPos > $startPos && ctype_space($html[$endPos])) {
                    $endPos--;
                }
                $endPos++; // Include the last character
            } else {
                $endPos = strlen($html);
            }
            
            $url = substr($html, $startPos, $endPos - $startPos);
            if (!empty($url)) {
                return $url;
            }
        }

        return null;
    }

    /**
     * Clean HTML from text
     *
     * @param string $text
     * @return string
     */
    public static function cleanHtmlFromText($text): string
    {
        if (empty($text)) {
            return '';
        }

        // Handle case where text might be an array (multilingual content)
        if (is_array($text)) {
            // Get the current locale's text, or fallback to first available
            $locale = app()->getLocale();
            $fallbackLocale = config('app.fallback_locale', 'en');
            
            $text = $text[$locale] ?? $text[$fallbackLocale] ?? array_values($text)[0] ?? '';
        }

        // Remove HTML img tags and other HTML
        $cleanText = preg_replace('/<img[^>]*>/i', '', $text);
        return trim(strip_tags($cleanText));
    }

    /**
     * Clean image URLs in an array of questions/options
     *
     * @param array $data
     * @return array
     */
    public static function cleanArray(array $data): array
    {
        return array_map(function ($item) {
            if (is_array($item)) {
                // Handle nested arrays
                $cleaned = self::cleanArray($item);
                
                // Clean image_url if present
                if (isset($cleaned['image_url'])) {
                    $cleaned['image_url'] = self::clean($cleaned['image_url']);
                }
                
                // Clean imgpath if present (from import data)
                if (isset($cleaned['imgpath'])) {
                    $cleaned['imgpath'] = self::clean($cleaned['imgpath']);
                }
                
                return $cleaned;
            }
            
            return $item;
        }, $data);
    }
    
    /**
     * Process option text to fix image URLs
     */
    public static function processOptionText($optionText)
    {
        // Handle if optionText is an array (translated content)
        if (is_array($optionText)) {
            return array_map(function($text) {
                return self::processOptionText($text);
            }, $optionText);
        }
        
        // Ensure it's a string
        if (!is_string($optionText)) {
            return $optionText;
        }
        
        if (preg_match_all('/<img[^>]*src=[\"\']?([^\s\"\'>]+)/i', $optionText, $matches)) {
            foreach ($matches[1] as $imgSrc) {
                // Convert relative paths to absolute URLs
                if (str_starts_with($imgSrc, '../examMedia/')) {
                    $newSrc = asset(str_replace('../', '', $imgSrc));
                    $optionText = str_replace($imgSrc, $newSrc, $optionText);
                } elseif (str_starts_with($imgSrc, 'examMedia/')) {
                    $newSrc = asset($imgSrc);
                    $optionText = str_replace($imgSrc, $newSrc, $optionText);
                }
            }
        }
        
        return $optionText;
    }
}
