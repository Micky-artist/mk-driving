<?php

use Carbon\Carbon;

if (!function_exists('timeDiffForHumans')) {
    /**
     * Calculate time difference and use custom translations instead of Carbon's auto-translation
     * 
     * @param mixed $date Carbon instance, date string, or timestamp
     * @param string $locale Target locale (defaults to current app locale)
     * @return string Formatted time difference using custom translations
     */
    function timeDiffForHumans($date, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        // Parse the date if it's not already a Carbon instance
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        $now = Carbon::now();
        
        // Use proper hierarchy: check largest unit first, stop at first non-zero
        if ($date->diffInYears($now) >= 1) {
            $years = intval($date->diffInYears($now));
            return trans_choice('time.year_ago', $years, ['count' => $years], $locale);
        }
        
        if ($date->diffInMonths($now) >= 1) {
            $months = intval($date->diffInMonths($now));
            return trans_choice('time.month_ago', $months, ['count' => $months], $locale);
        }
        
        if ($date->diffInWeeks($now) >= 1) {
            $weeks = intval($date->diffInWeeks($now));
            return trans_choice('time.week_ago', $weeks, ['count' => $weeks], $locale);
        }
        
        if ($date->diffInDays($now) >= 1) {
            $days = intval($date->diffInDays($now));
            return trans_choice('time.day_ago', $days, ['count' => $days], $locale);
        }
        
        if ($date->diffInHours($now) >= 1) {
            $hours = intval($date->diffInHours($now));
            return trans_choice('time.hour_ago', $hours, ['count' => $hours], $locale);
        }
        
        if ($date->diffInMinutes($now) >= 1) {
            $minutes = intval($date->diffInMinutes($now));
            return trans_choice('time.minute_ago', $minutes, ['count' => $minutes], $locale);
        }
        
        // Less than 1 minute
        $seconds = intval($date->diffInSeconds($now));
        return trans_choice('time.second_ago', $seconds, ['count' => $seconds], $locale);
    }
}

if (!function_exists('timeDiffForHumansFuture')) {
    /**
     * Calculate time difference for future dates and use custom translations
     * 
     * @param mixed $date Carbon instance, date string, or timestamp
     * @param string $locale Target locale (defaults to current app locale)
     * @return string Formatted time difference using custom translations
     */
    function timeDiffForHumansFuture($date, $locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        // Parse the date if it's not already a Carbon instance
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        $now = Carbon::now();
        
        // Use proper hierarchy: check largest unit first, stop at first non-zero
        if ($date->diffInYears($now) >= 1) {
            $years = intval($date->diffInYears($now));
            return trans_choice('time.year_from_now', $years, ['count' => $years], $locale);
        }
        
        if ($date->diffInMonths($now) >= 1) {
            $months = intval($date->diffInMonths($now));
            return trans_choice('time.month_from_now', $months, ['count' => $months], $locale);
        }
        
        if ($date->diffInWeeks($now) >= 1) {
            $weeks = intval($date->diffInWeeks($now));
            return trans_choice('time.week_from_now', $weeks, ['count' => $weeks], $locale);
        }
        
        if ($date->diffInDays($now) >= 1) {
            $days = intval($date->diffInDays($now));
            return trans_choice('time.day_from_now', $days, ['count' => $days], $locale);
        }
        
        if ($date->diffInHours($now) >= 1) {
            $hours = intval($date->diffInHours($now));
            return trans_choice('time.hour_from_now', $hours, ['count' => $hours], $locale);
        }
        
        if ($date->diffInMinutes($now) >= 1) {
            $minutes = intval($date->diffInMinutes($now));
            return trans_choice('time.minute_from_now', $minutes, ['count' => $minutes], $locale);
        }
        
        // Less than 1 minute
        $seconds = intval($date->diffInSeconds($now));
        return trans_choice('time.second_from_now', $seconds, ['count' => $seconds], $locale);
    }
}
