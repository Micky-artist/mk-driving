<?php

require_once __DIR__ . '/../../bootstrap/app.php';

use App\Services\ImageUrlCleaner;
use Illuminate\Support\Facades\DB;

echo "=== Image Reference Analysis ===\n\n";

// Get all actual files in examMedia
$examMediaPath = public_path('examMedia');
$actualFiles = [];
if (is_dir($examMediaPath)) {
    $files = scandir($examMediaPath);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
            $actualFiles[] = $file;
        }
    }
}

echo "Found " . count($actualFiles) . " JPG files in examMedia/\n\n";

// Get all image URLs from database
$questionImages = DB::table('questions')
    ->whereNotNull('image_url')
    ->where('image_url', '!=', '')
    ->pluck('image_url')
    ->toArray();

$optionImages = DB::table('options')
    ->whereNotNull('image_url')
    ->where('image_url', '!=', '')
    ->pluck('image_url')
    ->toArray();

// Also extract images from option_text (HTML img tags)
$optionTextImages = DB::table('options')
    ->whereNotNull('option_text')
    ->where('option_text', '!=', '')
    ->pluck('option_text')
    ->toArray();

$extractedFromText = [];
foreach ($optionTextImages as $optionText) {
    if (preg_match_all('/<img[^>]*src=[\"\']?([^\s\"\'>]+)/i', $optionText, $matches)) {
        foreach ($matches[1] as $imgSrc) {
            $extractedFromText[] = $imgSrc;
        }
    }
}

echo "Found " . count($questionImages) . " question images in database\n";
echo "Found " . count($optionImages) . " option image_url fields in database\n";
echo "Found " . count($extractedFromText) . " images in option_text fields\n\n";

// Clean and extract filenames
$cleanQuestionImages = [];
foreach ($questionImages as $imageUrl) {
    $cleaned = ImageUrlCleaner::clean($imageUrl);
    if ($cleaned && str_contains($cleaned, 'examMedia/')) {
        $filename = basename($cleaned);
        $cleanQuestionImages[] = $filename;
    }
}

$cleanOptionImages = [];
foreach ($optionImages as $imageUrl) {
    $cleaned = ImageUrlCleaner::clean($imageUrl);
    if ($cleaned && str_contains($cleaned, 'examMedia/')) {
        $filename = basename($cleaned);
        $cleanOptionImages[] = $filename;
    }
}

$cleanExtractedImages = [];
foreach ($extractedFromText as $imgSrc) {
    $cleaned = ImageUrlCleaner::clean($imgSrc);
    if ($cleaned && str_contains($cleaned, 'examMedia/')) {
        $filename = basename($cleaned);
        $cleanExtractedImages[] = $filename;
    }
}

echo "Cleaned question images: " . count($cleanQuestionImages) . "\n";
echo "Cleaned option image_url fields: " . count($cleanOptionImages) . "\n";
echo "Cleaned images from option_text: " . count($cleanExtractedImages) . "\n\n";

// Find matches and mismatches
$allReferencedImages = array_unique(array_merge($cleanQuestionImages, $cleanOptionImages, $cleanExtractedImages));
$matchingFiles = array_intersect($allReferencedImages, $actualFiles);
$missingFiles = array_diff($allReferencedImages, $actualFiles);
$unusedFiles = array_diff($actualFiles, $allReferencedImages);

echo "=== RESULTS ===\n";
echo "Images that exist AND are referenced: " . count($matchingFiles) . "\n";
echo "Images referenced but MISSING: " . count($missingFiles) . "\n";
echo "Images that exist but UNUSED: " . count($unusedFiles) . "\n\n";

if (!empty($missingFiles)) {
    echo "=== MISSING FILES WITH QUESTION DETAILS ===\n";
    
    // Get detailed information about missing image references
    $questionDetails = DB::table('questions as q')
        ->join('quizzes as quiz', 'q.quiz_id', '=', 'quiz.id')
        ->select('quiz.id as quiz_id', 'quiz.title', 'q.id as question_id', 'q.text', 'q.image_url')
        ->whereNotNull('q.image_url')
        ->where('q.image_url', '!=', '')
        ->get();
    
    $optionDetails = DB::table('options as o')
        ->join('questions as q', 'o.question_id', '=', 'q.id')
        ->join('quizzes as quiz', 'q.quiz_id', '=', 'quiz.id')
        ->select('quiz.id as quiz_id', 'quiz.title', 'q.id as question_id', 'q.text', 'o.option_text', 'o.id as option_id')
        ->whereNotNull('o.option_text')
        ->where('o.option_text', '!=', '')
        ->get();
    
    foreach ($missingFiles as $missingFile) {
        echo "\n--- MISSING: $missingFile ---\n";
        
        // Check questions table
        foreach ($questionDetails as $detail) {
            $cleaned = ImageUrlCleaner::clean($detail->image_url);
            if ($cleaned && str_contains($cleaned, 'examMedia/')) {
                $filename = basename($cleaned);
                if ($filename === $missingFile) {
                    echo "Quiz ID: {$detail->quiz_id} ({$detail->title})\n";
                    echo "Question ID: {$detail->question_id}\n";
                    echo "Question: " . substr($detail->text, 0, 100) . "...\n";
                    echo "Image URL: {$detail->image_url}\n\n";
                }
            }
        }
        
        // Check options table
        foreach ($optionDetails as $detail) {
            if (preg_match_all('/<img[^>]*src=[\"\']?([^\s\"\'>]+)/i', $detail->option_text, $matches)) {
                foreach ($matches[1] as $imgSrc) {
                    $cleaned = ImageUrlCleaner::clean($imgSrc);
                    if ($cleaned && str_contains($cleaned, 'examMedia/')) {
                        $filename = basename($cleaned);
                        if ($filename === $missingFile) {
                            echo "Quiz ID: {$detail->quiz_id} ({$detail->title})\n";
                            echo "Question ID: {$detail->question_id}\n";
                            echo "Question: " . substr($detail->text, 0, 100) . "...\n";
                            echo "Option ID: {$detail->option_id}\n";
                            echo "Option Text: " . substr($detail->option_text, 0, 150) . "...\n\n";
                        }
                    }
                }
            }
        }
    }
}

if (!empty($unusedFiles)) {
    echo "=== UNUSED FILES ===\n";
    foreach (array_slice($unusedFiles, 0, 20) as $file) {
        echo "- $file\n";
    }
    if (count($unusedFiles) > 20) {
        echo "... and " . (count($unusedFiles) - 20) . " more\n";
    }
    echo "\n";
}

echo "=== SAMPLE MATCHES ===\n";
foreach (array_slice($matchingFiles, 0, 10) as $file) {
    echo "- $file\n";
}
if (count($matchingFiles) > 10) {
    echo "... and " . (count($matchingFiles) - 10) . " more\n";
}
