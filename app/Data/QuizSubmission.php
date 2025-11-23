<?php

namespace App\Data;

use Illuminate\Http\Request;

class QuizSubmission
{
    /**
     * @var array<string, string> Map of question_id => option_id
     */
    private array $answers = [];
    
    /**
     * @var array<string, int> 
     */
    private array $timeSpent = [];
    
    private int $timeTaken;
    private ?string $endTime = null;
    private ?int $pausedTime = null;
    private ?bool $timeUp = null;

    public function getAnswers(): array
    {
        return $this->answers;
    }

    /**
     * @param array<string, string> $answers Map of question_id => option_id
     */
    public function setAnswers(array $answers): self
    {
        $this->answers = $answers;
        return $this;
    }

    /**
     * @return array<string, int>
     */
    public function getTimeSpent(): array
    {
        return $this->timeSpent;
    }

    /**
     * @param array<string, int> $timeSpent
     */
    public function setTimeSpent(array $timeSpent): self
    {
        $this->timeSpent = $timeSpent;
        return $this;
    }

    public function getTimeTaken(): int
    {
        return $this->timeTaken;
    }

    public function setTimeTaken(int $timeTaken): self
    {
        $this->timeTaken = $timeTaken;
        return $this;
    }

    public function getEndTime(): ?string
    {
        return $this->endTime;
    }

    public function setEndTime(?string $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getPausedTime(): ?int
    {
        return $this->pausedTime;
    }

    public function setPausedTime(?int $pausedTime): self
    {
        $this->pausedTime = $pausedTime;
        return $this;
    }

    public function isTimeUp(): ?bool
    {
        return $this->timeUp;
    }

    public function setTimeUp(?bool $timeUp): self
    {
        $this->timeUp = $timeUp;
        return $this;
    }

    public static function fromRequest(array $data): self
    {
        $submission = new self();
        
        // Set answers directly (already in question_id => option_id format)
        $submission->setAnswers($data['answers'] ?? []);
        
        // Set time spent (already in question_id => seconds format)
        $submission->setTimeSpent($data['time_spent'] ?? []);
        
        // Set other fields
        $submission->setTimeTaken((int)($data['time_taken'] ?? 0));
        $submission->setEndTime($data['end_time'] ?? null);
        $submission->setPausedTime(isset($data['paused_time']) ? (int)$data['paused_time'] : null);
        $submission->setTimeUp(isset($data['time_up']) ? (bool)$data['time_up'] : false);
        
        return $submission;
    }
}
