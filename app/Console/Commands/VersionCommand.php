<?php

namespace App\Console\Commands;

use App\Helpers\VersionHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class VersionCommand extends Command
{
    protected $signature = 'app:version 
                            {action : The version action (show, patch, minor, major, set)}
                            {--v= : Version number to set (only for set action)}
                            {--m|message= : Release message for the changelog}
                            {--d|dry-run : Show what would be done without making changes}';

    protected $description = 'Manage application versioning';

    public function handle()
    {
        $action = $this->argument('action');
        $dryRun = $this->option('dry-run');
        $message = $this->option('message') ?? 'No description provided';

        if (!in_array($action, ['show', 'patch', 'minor', 'major', 'set'])) {
            $this->error('Invalid action. Available actions: show, patch, minor, major, set');
            return 1;
        }

        if ($action === 'show') {
            return $this->showVersion();
        }

        if ($action === 'set' && !$this->option('v')) {
            $this->error('You must provide a version number with --v option');
            return 1;
        }

        if ($dryRun) {
            $this->info('[DRY RUN] No changes will be made');
        }

        switch ($action) {
            case 'patch':
                return $this->incrementPatch($dryRun, $message);
            case 'minor':
                return $this->incrementMinor($dryRun, $message);
            case 'major':
                return $this->incrementMajor($dryRun, $message);
            case 'set':
                return $this->setVersion($this->option('v'), $dryRun, $message);
        }

        return 0;
    }

    protected function showVersion()
    {
        $version = VersionHelper::getVersion();
        $build = VersionHelper::getBuildNumber();
        $releasedAt = VersionHelper::getReleaseDate()->format('Y-m-d H:i:s');
        $changelog = VersionHelper::getChangelog();

        $this->info("Version: $version");
        $this->info("Build: $build");
        $this->info("Released: $releasedAt");
        
        if (!empty($changelog)) {
            $this->newLine();
            $this->info('Changelog:');
            foreach (array_reverse($changelog) as $entry) {
                $this->line("- $entry");
            }
        }

        return 0;
    }

    protected function incrementPatch(bool $dryRun, string $message)
    {
        $this->info('Incrementing patch version...');
        
        if (!$dryRun) {
            VersionHelper::incrementPatch();
            $this->updateChangelog($message);
        }
        
        return $this->showVersion();
    }

    protected function incrementMinor(bool $dryRun, string $message)
    {
        $this->info('Incrementing minor version...');
        
        if (!$dryRun) {
            VersionHelper::incrementMinor();
            $this->updateChangelog($message);
        }
        
        return $this->showVersion();
    }

    protected function incrementMajor(bool $dryRun, string $message)
    {
        $this->info('Incrementing major version...');
        
        if (!$dryRun) {
            VersionHelper::incrementMajor();
            $this->updateChangelog($message);
        }
        
        return $this->showVersion();
    }

    protected function setVersion(string $version, bool $dryRun, string $message)
    {
        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            $this->error('Version must be in format x.y.z');
            return 1;
        }

        $this->info("Setting version to $version...");
        
        if (!$dryRun) {
            $versionFile = base_path('version.json');
            $versionData = json_decode(File::get($versionFile), true);
            $versionData['version'] = $version;
            $versionData['build']++;
            $versionData['released_at'] = now()->toIso8601String();
            
            File::put($versionFile, json_encode($versionData, JSON_PRETTY_PRINT));
            $this->updateChangelog($message);
        }
        
        return $this->showVersion();
    }

    protected function updateChangelog(string $message): void
    {
        $versionFile = base_path('version.json');
        $versionData = json_decode(File::get($versionFile), true);
        
        $versionData['changelog'][] = sprintf(
            '[%s] Version %s - %s',
            now()->toDateString(),
            $versionData['version'] . '.' . $versionData['build'],
            $message
        );
        
        // Keep only the last 20 changelog entries
        if (count($versionData['changelog']) > 20) {
            $versionData['changelog'] = array_slice($versionData['changelog'], -20);
        }
        
        File::put($versionFile, json_encode($versionData, JSON_PRETTY_PRINT));
    }
}
