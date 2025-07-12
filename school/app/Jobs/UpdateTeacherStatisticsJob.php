<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Teacher;

class UpdateTeacherStatisticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $teacherId;

    /**
     * Create a new job instance.
     */
    public function __construct($teacherId = null)
    {
        $this->teacherId = $teacherId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->teacherId) {
            $teacher = Teacher::where('users_id', $this->teacherId)->first();
            if ($teacher) {
                $teacher->calculateAndUpdateStatistics();
            }
        } else {
            $teachers = Teacher::all();
            foreach ($teachers as $teacher) {
                $teacher->calculateAndUpdateStatistics();
            }
        }
    }
}
