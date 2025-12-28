<?php

if (!function_exists('timeDiffForHumans')) {
    /**
     * Calculate time difference and use custom translations
     * 
     * @param mixed $date Date string or timestamp
     * @param string $locale Target locale (defaults to current app locale)
     * @return string Formatted time difference using custom translations
     */
    function timeDiffForHumans($date, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        // Parse the date to timestamp
        $timestamp = is_numeric($date) ? (int)$date : strtotime($date);
        $now = time();
        
        // Calculate differences in seconds
        $diff = $now - $timestamp;
        
        // Use proper hierarchy: check largest unit first, stop at first non-zero
        if ($diff >= 31536000) { // 365 days
            $years = floor($diff / 31536000);
            if ($locale === 'rw') {
                // Manual pluralization for Kinyarwanda
                $translation = $years === 1 ? 'umwaka :count ushize' : 'imyaka :count ishize';
                return str_replace(':count', $years, $translation);
            }
            return trans_choice('time.year_ago', $years, ['count' => $years], $locale);
        }
        
        if ($diff >= 2592000) { // 30 days
            $months = floor($diff / 2592000);
            if ($locale === 'rw') {
                // Manual pluralization for Kinyarwanda
                $translation = $months === 1 ? 'ukwezi :count gushize' : 'amezi :count ashize';
                return str_replace(':count', $months, $translation);
            }
            return trans_choice('time.month_ago', $months, ['count' => $months], $locale);
        }
        
        if ($diff >= 604800) { // 7 days
            $weeks = floor($diff / 604800);
            if ($locale === 'rw') {
                // Manual pluralization for Kinyarwanda
                $translation = $weeks === 1 ? 'icyumweru :count gishize' : 'ibyumweru :count bishize';
                return str_replace(':count', $weeks, $translation);
            }
            return trans_choice('time.week_ago', $weeks, ['count' => $weeks], $locale);
        }
        
        if ($diff >= 86400) { // 1 day
            $days = floor($diff / 86400);
            if ($locale === 'rw') {
                // Manual pluralization for Kinyarwanda
                $translation = $days === 1 ? 'umunsi :count ushize' : 'iminsi :count ishize';
                return str_replace(':count', $days, $translation);
            }
            return trans_choice('time.day_ago', $days, ['count' => $days], $locale);
        }
        
        if ($diff >= 3600) { // 1 hour
            $hours = floor($diff / 3600);
            if ($locale === 'rw') {
                // Manual pluralization for Kinyarwanda
                $translation = $hours === 1 ? 'isaha :count ishize' : 'amasaha :count ashize';
                return str_replace(':count', $hours, $translation);
            }
            return trans_choice('time.hour_ago', $hours, ['count' => $hours], $locale);
        }
        
        if ($diff >= 60) { // 1 minute
            $minutes = floor($diff / 60);
            $key = 'time.minute_ago';
            if ($locale === 'rw') {
                // Manual pluralization for Kinyarwanda
                $translation = $minutes === 1 ? 'umunota :count ushize' : 'iminota :count ishize';
                return str_replace(':count', $minutes, $translation);
            }
            return trans_choice($key, $minutes, ['count' => $minutes], $locale);
        }
        
        // Less than 1 minute
        if ($locale === 'rw') {
            // Manual pluralization for Kinyarwanda
            $translation = $diff === 1 ? 'isegonda :count rishize' : 'amasegonda :count ashize';
            return str_replace(':count', $diff, $translation);
        }
        return trans_choice('time.second_ago', $diff, ['count' => $diff], $locale);
    }
}

if (!function_exists('timeDiffForHumansFuture')) {
    /**
     * Calculate time difference for future dates and use custom translations
     * 
     * @param mixed $date Date string or timestamp
     * @param string $locale Target locale (defaults to current app locale)
     * @return string Formatted time difference using custom translations
     */
    function timeDiffForHumansFuture($date, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        // Parse the date to timestamp
        $timestamp = is_numeric($date) ? (int)$date : strtotime($date);
        $now = time();
        
        // Calculate differences in seconds (future dates will be positive)
        $diff = $timestamp - $now;
        
        // Use proper hierarchy: check largest unit first, stop at first non-zero
        if ($diff >= 31536000) { // 365 days
            $years = floor($diff / 31536000);
            return trans_choice('time.year_from_now', $years, ['count' => $years], $locale);
        }
        
        if ($diff >= 2592000) { // 30 days
            $months = floor($diff / 2592000);
            return trans_choice('time.month_from_now', $months, ['count' => $months], $locale);
        }
        
        if ($diff >= 604800) { // 7 days
            $weeks = floor($diff / 604800);
            return trans_choice('time.week_from_now', $weeks, ['count' => $weeks], $locale);
        }
        
        if ($diff >= 86400) { // 1 day
            $days = floor($diff / 86400);
            return trans_choice('time.day_from_now', $days, ['count' => $days], $locale);
        }
        
        if ($diff >= 3600) { // 1 hour
            $hours = floor($diff / 3600);
            return trans_choice('time.hour_from_now', $hours, ['count' => $hours], $locale);
        }
        
        if ($diff >= 60) { // 1 minute
            $minutes = floor($diff / 60);
            return trans_choice('time.minute_from_now', $minutes, ['count' => $minutes], $locale);
        }
        
        // Less than 1 minute
        return trans_choice('time.second_from_now', $diff, ['count' => $diff], $locale);
    }
}

if (!function_exists('formatDate')) {
    /**
     * Format date using PHP date function
     * 
     * @param mixed $date Date string or timestamp
     * @param string $format Date format string
     * @return string Formatted date
     */
    function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        $timestamp = is_numeric($date) ? (int)$date : strtotime($date);
        return date($format, $timestamp);
    }
}

if (!function_exists('subDays')) {
    /**
     * Subtract days from current time
     * 
     * @param int $days Number of days to subtract
     * @return int Timestamp for the date days ago
     */
    function subDays($days)
    {
        return time() - ($days * 86400);
    }
}

if (!function_exists('subMonths')) {
    /**
     * Subtract months from current time
     * 
     * @param int $months Number of months to subtract
     * @return int Timestamp for the date months ago
     */
    function subMonths($months)
    {
        return strtotime("-{$months} months");
    }
}

if (!function_exists('startOfMonth')) {
    /**
     * Get timestamp for start of current month
     * 
     * @return int Timestamp for start of current month
     */
    function startOfMonth()
    {
        return strtotime(date('Y-m-01 00:00:00'));
    }
}

if (!function_exists('addDays')) {
    /**
     * Add days to a given date
     * 
     * @param mixed $date Date string or timestamp
     * @param int $days Number of days to add
     * @return int Timestamp for the future date
     */
    function addDays($date, $days)
    {
        $timestamp = is_numeric($date) ? (int)$date : strtotime($date);
        return strtotime("+{$days} days", $timestamp);
    }
}

if (!function_exists('currentTimestamp')) {
    /**
     * Get current timestamp
     * 
     * @return int Current timestamp
     */
    function currentTimestamp()
    {
        return time();
    }
}

if (!function_exists('atomString')) {
    /**
     * Get current time in ATOM format
     * 
     * @return string Current time in ATOM format
     */
    function atomString()
    {
        return date('c');
    }
}
