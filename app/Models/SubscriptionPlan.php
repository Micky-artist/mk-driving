<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'slug',
        'name',
        'description',
        'price',
        'duration',
        'features',
        'is_active',
        'max_quizzes',
        'color',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'price' => 'float',
        'duration' => 'integer',
        'features' => 'array',
        'is_active' => 'boolean',
        'max_quizzes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Get the features for the current locale.
     *
     * @return array
     */
    public function getFeaturesAttribute($value)
    {
        // If value is already an array, use it directly, otherwise try to decode JSON
        $features = is_array($value) ? $value : (json_decode($value, true) ?: []);
        
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');
        
        // If features is empty or not an array, return empty array
        if (!is_array($features) || empty($features)) {
            return [];
        }
        
        // Check if features is a simple array (no language codes)
        if (array_values($features) === $features) {
            return array_values($features);
        }
        
        // If features has language codes as keys (e.g., 'en' => [...], 'rw' => [...])
        if (isset($features[$locale]) && is_array($features[$locale])) {
            return array_values($features[$locale]);
        }
        
        if (isset($features[$fallback]) && is_array($features[$fallback])) {
            return array_values($features[$fallback]);
        }
        
        // If we have a single feature that's a string, return it as an array
        if (is_string($features)) {
            return [$features];
        }
        
        // Last resort: return the features as-is, but ensure it's an array
        return is_array($features) ? array_values($features) : [];
    }

    /**
     * Get the quizzes associated with this subscription plan.
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'subscription_plan_slug', 'slug');
    }
    
    /**
     * Get a translation for a given field
     *
     * @param string $field The field to get the translation for (name, description)
     * @param string|null $locale The locale to get the translation for
     * @param bool $useFallback Whether to fall back to the default locale if the translation is not found
     * @return string
     */
    public function getTranslation(string $field, ?string $locale = null, bool $useFallback = true): string
    {
        $locale = $locale ?: app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        
        $value = $this->$field;
        
        if (is_array($value)) {
            if (!empty($value[$locale])) {
                return $value[$locale];
            }
            
            if ($useFallback && !empty($value[$fallbackLocale])) {
                return $value[$fallbackLocale];
            }
            
            // If no translation found, return the first available or an empty string
            return !empty($value) ? reset($value) : '';
        }
        
        return $value ?? '';
    }

    /**
     * Get the subscriptions associated with this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'subscription_plan_id');
    }
}
