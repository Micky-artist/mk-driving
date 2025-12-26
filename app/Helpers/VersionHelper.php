<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class VersionHelper
{
    protected static $versionFile;
    protected static $versionData;

    public static function init()
    {
        self::$versionFile = base_path('version.json');
        
        if (!File::exists(self::$versionFile)) {
            self::createInitialVersionFile();
        }
        
        self::$versionData = json_decode(File::get(self::$versionFile), true);
    }

    public static function getVersion(): string
    {
        self::init();
        return self::$versionData['version'];
    }

    public static function getBuildNumber(): int
    {
        self::init();
        return self::$versionData['build'];
    }

    public static function getFullVersion(): string
    {
        return sprintf('v%s+%d', self::getVersion(), self::getBuildNumber());
    }

    public static function getChangelog(): array
    {
        self::init();
        return self::$versionData['changelog'] ?? [];
    }

    public static function getReleaseDate()
    {
        self::init();
        return self::$versionData['released_at'];
    }

    public static function incrementPatch(): void
    {
        self::incrementVersion('patch');
    }

    public static function incrementMinor(): void
    {
        self::incrementVersion('minor');
    }

    public static function incrementMajor(): void
    {
        self::init();
        list($major, $minor, $patch) = explode('.', self::$versionData['version']);
        $newVersion = (int)$major + 1 . '.0.0';
        self::updateVersion($newVersion, 'major');
    }

    protected static function incrementVersion(string $type): void
    {
        self::init();
        list($major, $minor, $patch) = explode('.', self::$versionData['version']);
        
        $newVersion = match($type) {
            'patch' => "$major.$minor." . ((int)$patch + 1),
            'minor' => "$major." . ((int)$minor + 1) . ".0",
            default => self::$versionData['version']
        };
        
        self::updateVersion($newVersion, $type);
    }

    protected static function updateVersion(string $version, string $type): void
    {
        self::init();
        
        self::$versionData['version'] = $version;
        self::$versionData['build']++;
        self::$versionData['released_at'] = date('c');
        
        // Add to changelog
        self::$versionData['changelog'][] = sprintf(
            '[%s] Version %s (%s) - %s',
            date('Y-m-d'),
            self::getFullVersion(),
            $type,
            'Update description here' // This should be passed as parameter in a real scenario
        );
        
        // Keep only the last 20 changelog entries
        if (count(self::$versionData['changelog']) > 20) {
            self::$versionData['changelog'] = array_slice(self::$versionData['changelog'], -20);
        }
        
        File::put(self::$versionFile, json_encode(self::$versionData, JSON_PRETTY_PRINT));
        
        // Clear any cached version data
        Cache::forget('app.version');
    }

    protected static function createInitialVersionFile(): void
    {
        $initialData = [
            'version' => '1.0.0',
            'build' => 1,
            'released_at' => date('c'),
            'changelog' => [
                '[' . date('Y-m-d') . '] Initial version created' 
            ]
        ];
        
        File::put(self::$versionFile, json_encode($initialData, JSON_PRETTY_PRINT));
    }
}
