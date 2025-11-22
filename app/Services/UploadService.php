<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadService
{
    protected string $uploadPath = 'uploads';

    public function __construct()
    {
        $this->ensureUploadDirectoryExists();
    }

    protected function ensureUploadDirectoryExists(): void
    {
        if (!Storage::exists($this->uploadPath)) {
            Storage::makeDirectory($this->uploadPath);
        }
    }

    public function getFileValidationRules(string $fieldName = 'file'): array
    {
        return [
            $fieldName => [
                'required',
                'file',
                'mimes:jpeg,png,jpg,gif,svg,csv',
                'max:5120', // 5MB
            ]
        ];
    }

    public function upload(UploadedFile $file, string $directory = ''): string
    {
        $path = $directory ? "{$this->uploadPath}/{$directory}" : $this->uploadPath;
        $fileName = $this->generateFileName($file);
        
        return $file->storeAs($path, $fileName, 'public');
    }

    public function getFileUrl(string $path): string
    {
        return Storage::url($path);
    }

    public function deleteFile(string $path): bool
    {
        if (Storage::exists($path)) {
            return Storage::delete($path);
        }
        return false;
    }

    public function deleteFiles(array $paths): void
    {
        foreach ($paths as $path) {
            $this->deleteFile($path);
        }
    }

    protected function generateFileName(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($baseName);
        
        return $safeName . '-' . uniqid() . '.' . $extension;
    }

    public function getFileMimeType(string $path): string
    {
        return Storage::mimeType($path);
    }

    public function getFileSize(string $path): int
    {
        return Storage::size($path);
    }
}
