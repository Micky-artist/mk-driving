<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UploadController extends Controller
{
    protected UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }
    
    /**
     * Upload an image file
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:5120', // 5MB
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');
        
        try {
            $path = $this->uploadService->upload($file, 'images');
            $url = $this->uploadService->getFileUrl($path);
            
            return response()->json([
                'url' => $url,
                'filename' => basename($path),
                'originalname' => $file->getClientOriginalName(),
                'mimetype' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload file',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Upload a document file (PDF, DOC, etc.)
     */
    public function uploadDocument(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,txt',
                'max:10240', // 10MB
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $file = $request->file('file');
        
        try {
            $path = $this->uploadService->upload($file, 'documents');
            $url = $this->uploadService->getFileUrl($path);
            
            return response()->json([
                'url' => $url,
                'filename' => basename($path),
                'originalname' => $file->getClientOriginalName(),
                'mimetype' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload file',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete an uploaded file
     */
    public function deleteFile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $path = parse_url($request->url, PHP_URL_PATH);
            $path = ltrim($path, '/storage/');
            
            if ($this->uploadService->deleteFile($path)) {
                return response()->json([
                    'message' => 'File deleted successfully',
                    'deleted' => true
                ]);
            }
            
            return response()->json([
                'message' => 'File not found or could not be deleted',
                'deleted' => false
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete file',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Bulk delete files
     */
    public function bulkDeleteFiles(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'urls' => 'required|array',
            'urls.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $deleted = [];
        $failed = [];

        foreach ($request->urls as $url) {
            try {
                $path = parse_url($url, PHP_URL_PATH);
                $path = ltrim($path, '/storage/');
                
                if ($this->uploadService->deleteFile($path)) {
                    $deleted[] = $url;
                } else {
                    $failed[] = $url;
                }
            } catch (\Exception $e) {
                $failed[] = [
                    'url' => $url,
                    'error' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'message' => 'Bulk delete operation completed',
            'deleted' => $deleted,
            'failed' => $failed
        ]);
    }
}
