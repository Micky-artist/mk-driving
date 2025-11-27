<?php

// Load the questions from the JSON file
$questionsJson = file_get_contents(__DIR__ . '/../../app/Console/Commands/questions.json');
$quizzes = json_decode($questionsJson, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

$totalQuizzes = count($quizzes);
$quizzesWith20Questions = 0;
$quizzesWithAllNullImgPath = 0;
$totalQuestionsWithNullImgPath = 0;

foreach ($quizzes as $quizIndex => $quiz) {
    $quizNumber = $quizIndex + 1;
    $questionCount = count($quiz);
    $hasAllNullImgPath = true;
    $nullImgPathCount = 0;

    // Check each question in the quiz
    foreach ($quiz as $question) {
        if (empty($question['imgpath']) || strpos($question['imgpath'], 'null') !== false) {
            $nullImgPathCount++;
        } else {
            $hasAllNullImgPath = false;
        }
    }

    // Update counters
    if ($questionCount === 20) {
        $quizzesWith20Questions++;
    }

    if ($hasAllNullImgPath) {
        $quizzesWithAllNullImgPath++;
    }

    $totalQuestionsWithNullImgPath += $nullImgPathCount;

    // Output per-quiz stats
    echo "Quiz {$quizNumber}: {$questionCount} questions, " . 
         "Questions with null imgPath: {$nullImgPathCount}" . 
         ($hasAllNullImgPath ? " (ALL NULL)" : "") . "\n";
}

// Output summary
echo "\n=== Summary ===\n";
echo "Total quizzes: {$totalQuizzes}\n";
echo "Quizzes with exactly 20 questions: {$quizzesWith20Questions} (" . 
     round(($quizzesWith20Questions / $totalQuizzes) * 100, 2) . "%)\n";
echo "Quizzes with all questions having null imgPath: {$quizzesWithAllNullImgPath} (" . 
     round(($quizzesWithAllNullImgPath / $totalQuizzes) * 100, 2) . "%)\n";
echo "Total questions with null imgPath: {$totalQuestionsWithNullImgPath}\n";

// Check if all quizzes have 20 questions
if ($quizzesWith20Questions === $totalQuizzes) {
    echo "\n✅ All quizzes have exactly 20 questions.\n";
} else {
    echo "\n❌ Not all quizzes have exactly 20 questions.\n";
}
