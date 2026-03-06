<?php

return [
    'not_found' => 'Active quiz with ID :id not found.',
    'type' => [
        'regular' => 'Quiz',
        'guest' => 'Guest Quiz'
    ],
    'default' => [
        'title' => 'Untitled Quiz',
        'description' => 'No description available.',
        'topics' => ['General Knowledge']
    ],
    'errors' => [
        'no_translation' => 'No translation available',
    ],
    
    // Quiz UI Text
    'questions' => 'Questions',
    'question' => 'Question',
    'of' => 'of',
    'timeLimit' => 'Time Limit',
    'timeRemaining' => 'Time Remaining',
    'minutes' => 'minutes',
    'previousAttempts' => 'Previous Attempts',
    'explanation' => 'Explanation',
    'submitQuiz' => 'Submit Quiz',
    'isLoading' => 'Loading...',
    'isSubmitting' => 'Submitting...',
    'viewResults' => 'View Detailed Results',
    'confirmSubmit' => 'Are you sure you want to submit your answers? You won\'t be able to change them after submission.',
    'timeUp' => 'Time\'s Up!',
    'quizSubmitted' => 'Quiz Submitted!',
    'yourScoreIs' => 'Your score is :score out of :total',
    'passedMessage' => 'Congratulations! You passed the quiz!',
    'failedMessage' => 'You didn\'t pass this time. Keep practicing!',
    'processing' => 'Processing...',
    'nextQuestion' => 'Next Question',
    'previousQuestion' => 'Previous Question',
    'completed' => 'Completed',
    'inProgress' => 'In Progress',
    'passed' => 'Passed',
    'failed' => 'Failed',
    'startQuiz' => 'Start Quiz',
    'continueQuiz' => 'Continue Quiz',
    'noTimeLimit' => 'No time limit',
    'questionCount' => '{0} No questions|{1} 1 question|[2,*] :count questions',
    'timeLimitUnit' => '{1} :count minute|[2,*] :count minutes',
    'score' => 'Score: :score/:total',
    'percentage' => '(:percentage%)',
    'correctAnswer' => 'Correct Answer',
    'yourAnswer' => 'Your Answer',
    'correctAnswerFeedback' => 'Correct! Well done.',
    'incorrectAnswerFeedback' => 'Not quite. The correct answer is:',
    
    // Guest Quiz Carousel
    'guestQuiz' => [
        'practiceHere' => 'Practice here. Now!',
        'correctFeedback' => [
            'keepItUp' => 'Keep it up!',
            'greatJob' => 'Great job!',
            'youGotThis' => 'You got this!',
            'niceWork' => 'Nice work!',
            'wellDone' => 'Well done!',
            'amazing' => 'Amazing!',
            'fantastic' => 'Fantastic!',
            'superb' => 'Superb!'
        ],
        'wrongFeedback' => 'Not quite right. Try again!',
        'testYourKnowledge' => 'Test Your Knowledge!',
        'noQuizAvailable' => 'No quiz available at the moment.',
        'correct' => 'Correct!',
        'incorrect' => 'Incorrect!',
        'correctAnswerIs' => 'The correct answer is:',
        'tryAgain' => 'Try Again',
        'nextQuestion' => 'Next Question',
        'questions' => 'Questions',
        'startQuiz' => 'Start Quiz',
        'continueQuiz' => 'Continue Quiz',
        'viewDetails' => 'View Details',
        'time' => 'Time',
        'difficulty' => 'Difficulty',
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard',
        'expert' => 'Expert',
        'free' => 'Free',
        'premium' => 'Premium',
        'tryThisSampleQuestion' => 'Try this sample question',
        'showAnswer' => 'Show answer',
        'startFullQuiz' => 'Start full quiz',
        'correct' => 'Correct!',
        'incorrect' => 'Incorrect!',
        'challengingQuestions' => '{0} No questions|{1} 1 challenging question|[2,*] :count challenging questions',
        'noRegistrationNeeded' => 'No registration needed',
        'unlockAllQuestions' => 'Unlock all questions and track your progress!',
        'noQuizzesAvailable' => 'There are currently no quizzes available. Please check back later or contact support if you believe this is an error.',
        'exploreMore' => 'Explore More',
        'showCorrectAnswer' => 'Show correct answer',
        'goToSlide' => 'Go to slide :number'
    ],
    'reviewAnswers' => 'Review Your Answers',
    'backToQuiz' => 'Back to Quiz',
    'answerRequired' => 'Please select an answer before continuing.',
    'quizComplete' => 'Quiz Complete!',
    'yourResults' => 'Your Results',
    'correctAnswers' => 'Correct Answers: :count',
    'incorrectAnswers' => 'Incorrect Answers: :count',
    'timeSpent' => 'Time Spent: :time',
    'passedQuiz' => 'You passed the quiz!',
    'failedQuiz' => 'You didn\'t pass this time. Keep practicing!',
    'passingScore' => 'Passing Score: :score%',
    'yourScore' => 'Your Score: :score%',
    'retakeQuiz' => 'Retake Quiz',
    'viewExplanation' => 'View Explanation',
    'hideExplanation' => 'Hide Explanation',
    'allQuestionsAnswered' => 'All questions answered',
    'questionsRemaining' => ':count questions remaining',
    'questionNumber' => 'Question :current of :total',
    'submitConfirmTitle' => 'Submit Quiz?',
    'submitConfirmText' => 'Are you sure you want to submit your answers?',
    'cancel' => 'Cancel',
    'confirm' => 'Confirm',
    'loading' => 'Loading...',
    'errorLoadingQuiz' => 'Error loading quiz. Please try again.',
    'sessionExpired' => 'Your quiz session has expired. Please start again.',
    'connectionError' => 'Connection error. Please check your internet connection.',
    
    // Quiz UI Elements
    'attempt' => 'Attempt :number',
    'bestScore' => 'Best: :score%',
    'dayStreak' => ':count day streak|:count days streak',
    'autoNext' => 'Auto Next',
    'resetQuiz' => 'Reset Quiz',
    'addToBookmarks' => 'Add to bookmarks',
    'removeFromBookmarks' => 'Remove from bookmarks',
    'minutes' => 'minute|minutes',
    'questions' => 'question|questions',
    'serverError' => 'Server error. Please try again later.',
    
    // Quiz taking UI
    'resetQuiz' => 'Reset Quiz',
    'resetConfirmation' => 'Are you sure you want to reset the quiz? Your progress will be lost and you\'ll start over.',
    'quizCompleted' => 'Quiz Completed!',
    'quizInProgress' => 'Quiz In Progress',
    'selectAnswer' => 'Select an answer',
    'answerSaved' => 'Your answer has been saved',
    'saving' => 'Saving...',
    'quizPaused' => 'Quiz Paused',
    'quizPausedMessage' => 'Take a break. Your progress is saved.',
    'resumeQuiz' => 'Resume Quiz',
    'exitQuiz' => 'Exit Quiz',
    'previous' => 'Previous',
    'next' => 'Next',
    'submitQuiz' => 'Submit Quiz',
    'correct' => 'Correct',
    'incorrect' => 'Incorrect',
    'results' => 'Results',
    'youGot' => 'You got',
    'outOf' => 'out of',
    'questionsCorrect' => 'questions correct',
    'time' => 'Time',
    'accuracy' => 'Accuracy',
    'personalBest' => 'Personal Best',
    'improvedBy' => 'Improved by',
    'perfectScore' => 'Perfect Score!',
    'allQuestionsCorrect' => 'You answered all questions correctly!',
    'excellent' => 'Excellent!',
    'scoredAbove90' => 'You scored above 90%!',
    'yourProgress' => 'Your Progress',
    'currentScore' => 'Current Score',
    'previousBest' => 'Previous Best',
    'improvement' => 'Improvement',
    'reviewAnswers' => 'Review Answers',
    'retakeQuiz' => 'Retake Quiz',
    'backToQuizzes' => 'Back to Quizzes',
    'allQuestionsAnswered' => 'All questions answered!',
    'someQuestionsUnanswered' => 'You have some unanswered questions. Are you sure you want to submit anyway?',
    'submitAnyway' => 'Yes, submit anyway',
    'returnToQuiz' => 'Return to Quiz',
    'correctAnswers' => 'Correct Answers',
    'incorrectAnswers' => 'Incorrect Answers',
    'needToPass' => 'Pass rate:',
    'skippedQuestions' => 'Skipped Questions',
    'timeSpent' => 'Time Spent',
    'minutesShort' => 'min',
    'secondsShort' => 'sec',
    'questionNumber' => 'Question :current of :total',
    
    // Signup Nudge
    'signupNudge' => [
        'title' => 'Save Your Progress',
        'message' => 'Sign up to save your quiz progress and access more practice tests!',
        'signUpFree' => 'Sign Up Free',
        'haveAccount' => 'I Already Have an Account',
        'continueAsGuest' => 'Continue as Guest',
        'backHomepage' => 'Back to Homepage',
        'close' => 'Close'
    ],
    
    // Quiz Navigation
    'next' => 'Next',
    
    // Quiz Limit Messages
    'quizLimitReached' => 'Daily Quiz Limit Reached',
    'quizLimitMessage' => 'You can retake this quiz in :time. Upgrade to a premium plan for unlimited practice!',
    'upgradeForUnlimited' => 'Unlock unlimited quiz attempts and access to all premium features.',
    'upgradeNow' => 'Upgrade Now',
    'cancel' => 'Cancel',
    'resetRestricted' => 'Reset Restricted',
    'resetRestrictedMessage' => 'To reset this quiz, please upgrade to a premium plan for unlimited attempts and features.',
    'upgradeBenefits' => 'Premium Benefits',
    'unlimitedAttempts' => 'Unlimited quiz attempts',
    'noWaitingTime' => 'No waiting time between attempts',
    'fullAccess' => 'Full access to all premium features',
    'auto' => 'Auto Next',
    'previous' => 'Previous',
    'resetQuiz' => 'Reset Quiz',
    
    // Feedback
    'correct' => 'Correct!',
    'incorrect' => 'Incorrect',
    'correctFeedback' => 'Correct! Well done! 🎉',
    'incorrectFeedback' => 'Incorrect. The correct answer has been highlighted.',
    
    // Quiz Results Modal
    'quizCompleted' => 'Quiz Completed!',
    'youGotXOutOfY' => 'You got :correct out of :total questions correct',
    'timeTaken' => 'Time Taken',
    'wantMoreQuizzes' => 'Want More Quizzes?',
    'signupCta' => 'Create a free account to access hundreds of quizzes, track your progress, and compete with others!',
    'signUpFree' => 'Sign Up Free',
    'logIn' => 'Log In',
    'reviewAnswers' => 'Review Answers',
    'takeQuizAgain' => 'Take Quiz Again',
    'shareResults' => 'Share your results',
    
    // Premium Upgrade
    'upgradeToPremium' => 'Upgrade to Premium',
    'premiumBenefits' => 'Get unlimited access to all quizzes, detailed analytics, and AI-powered study recommendations!',
    'viewPlans' => 'View Plans',
    
    // Reset Confirmation
    'resetConfirmation' => 'Are you sure you want to reset the quiz? All your progress will be lost.',
    
    // Subscription Plans
    'wantMorePractice' => 'Want more practice?',
    'unlockFullAccess' => 'Unlock full access to all practice tests, detailed explanations, and track your progress!',
    
    // Unified Quiz Taker Component
    'previous' => 'Previous',
    'signUpToContinue' => 'Sign Up to Continue',
    'finish' => 'Finish',
    'next' => 'Next',
    'signUpRequired' => 'Sign Up Required',
    'signUpRequiredMessage' => 'Please login or sign up to continue with the quiz and save your progress.',
    'signUp' => 'Sign Up',
    'login' => 'Login',
    'cancel' => 'Cancel',
    'termsAndPrivacy' => 'By signing up, you agree to our Terms of Service and Privacy Policy',
    'questionImage' => 'Question image',
    'optionImage' => 'Option image',
    'loginToUsePause' => 'Login to use pause',
    'resume' => 'Resume',
    'pause' => 'Pause',
    'resetQuiz' => 'Reset quiz',
    'loginToFlagQuestions' => 'Login to flag questions',
    'flagQuestion' => 'Flag question',
    'loginToUseAutoAdvance' => 'Login to use auto-advance',
    'toggleAutoNext' => 'Toggle auto-next',
    'loginToSaveProgress' => 'Please login to save your progress and access all features.',
    
    // Leaderboard and Achievement Messages
    'newPosition' => 'New Position',
    'firstPlace' => 'First Place, Keep Going!',
    'firstPlaceMessage' => 'Keep up the great work!',
    'topTenMessage' => 'Great job! You\'re in the top 10!',
    'keepImproving' => 'Keep practicing to climb the leaderboard!',
    'seeMore' => 'See More',
    'practiceAgain' => 'Practice Again',
    'submittingMessage' => 'Please wait while we save your results...',

    // Results Modal
    'scoreEarned' => 'Score Earned',
    'averageScore' => 'Average Score',
    'practiceAgain' => 'Practice Again',
    'moreQuizzes' => 'More Quizzes',
    'upgradeSubscription' => 'Upgrade Subscription',
    'checkProgress' => 'Check Progress',
    'unlockMoreFeatures' => 'Unlock More Features!',
    'subscriptionMessage' => 'Get a subscription to practice unlimited quizzes and track your progress.',
    'getPlanToPractice' => 'Get Plan to Practice More',
    'close' => 'Close',

    // Robot Companion Messages
    'companion' => [
        'title' => 'Others Practicing Here',
        'robot_correct' => ':name just got this right!',
        'robot_nailed' => ':name nailed this one!',
        'robot_aced' => ':name aced this question!',
        'robot_mastered' => ':name mastered this!',
        'robot_wrong' => ':name just got it wrong',
        'robot_tricky' => ':name found this tricky',
        'robot_missed' => ':name missed this one',
        'robot_struggled' => ':name struggled with this one',
        
        // Learner Messages
        'learner_correct' => ':name just got this right!',
        'learner_nailed' => ':name nailed this one!',
        'learner_got_correct' => ':name got it correct!',
        'learner_aced' => ':name aced this question!',
        'learner_mastered' => ':name mastered this!',
        'learner_figured' => ':name figured it out!',
        'learner_got_right' => ':name got this one right!',
        'justEarnedPoints' => ':name just earned :points points!',
        'learner_wrong' => ':name just got it wrong',
        'learner_struggled' => ':name struggled with this one',
        'learner_tricky' => ':name found this tricky',
        'learner_wrong_too' => ':name got this wrong too',
        'learner_missed' => ':name missed this one',
        'learner_didnt_get' => ':name didn\'t get this one',
        'learner_wrong_dont_worry' => ':name got it wrong - don\'t worry',
        
        // Competitive Messages (fallbacks)
        'both_correct' => ':learnerName got it right too!',
        'user_correct_learner_wrong' => ':learnerName is still learning!',
        'user_wrong_learner_correct' => ':learnerName got this one!',
        'both_wrong' => ':learnerName is learning too!',
        'default_message' => ':learnerName is working!',
        
        // Personality-based competitive messages
        'supportive' => [
            'both_correct' => [
                ':learnerName got it right too! Great minds think alike!',
                'Nice! :learnerName also got this one correct.',
                ':learnerName is on fire with that correct answer!'
            ],
            'user_correct_learner_wrong' => [
                ':learnerName is still learning this one, but you got it!',
                'Don\'t worry, :learnerName will get the next one!',
                ':learnerName is catching up to your correct answer!'
            ],
            'user_wrong_learner_correct' => [
                ':learnerName got this one! You\'ll get the next one!',
                'Nice job by :learnerName! Keep trying!',
                ':learnerName shows how it\'s done! You can do this too!'
            ],
            'both_wrong' => [
                ':learnerName is working through this one too!',
                'Both :learnerName and you are learning together!',
                ':learnerName is figuring this out, just like you!'
            ]
        ],
        'competitive' => [
            'both_correct' => [
                ':learnerName also nailed it! This is a race!',
                ':learnerName is keeping up with your correct answer!',
                'Both you and :learnerName got this right! Who\'s faster?'
            ],
            'user_correct_learner_wrong' => [
                'You got it but :learnerName didn\'t! You\'re winning!',
                ':learnerName missed this one but you didn\'t!',
                'Your correct answer puts you ahead of :learnerName!'
            ],
            'user_wrong_learner_correct' => [
                ':learnerName got this one and you didn\'t!',
                ':learnerName is taking the lead with that answer!',
                ':learnerName shows you how it\'s done this time!'
            ],
            'both_wrong' => [
                'Both you and :learnerName missed this one!',
                ':learnerName is struggling too, keep pushing!',
                'This question got both you and :learnerName!'
            ]
        ],
        'friendly' => [
            'both_correct' => [
                ':learnerName got it right too! High five!',
                'Awesome! :learnerName also got this correct!',
                ':learnerName is rocking this quiz with you!'
            ],
            'user_correct_learner_wrong' => [
                'You got it! :learnerName will get the next one!',
                'Nice work! :learnerName is learning from you!',
                ':learnerName needs your help on this one!'
            ],
            'user_wrong_learner_correct' => [
                ':learnerName got this one! Team effort!',
                ':learnerName got it! Let\'s help each other!',
                'Great job :learnerName! We\'ll get the next one!'
            ],
            'both_wrong' => [
                ':learnerName is learning with you on this one!',
                'Team struggle! You and :learnerName will get the next one!',
                ':learnerName is in the same boat, keep going!'
            ]
        ]
    ],
    
    // Additional companion keys
    'points' => 'points',
    'startQuizToSeeActivity' => 'Start a quiz to see live activity',
];
