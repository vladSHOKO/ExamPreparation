<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method tasks()
 */
class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $completedTasks = $this->tasks()
            ->where('status', 'completed')
            ->pluck('task_id')
            ->toArray();

        $allTasks = $this->tasks()->count();
        $completedCount = count($completedTasks);

        return [
            'student_id' => $this->id,
            'login' => $this->user->login,
            'completed_tasks' => $completedTasks,
            'teacher_id' => $this->teacher->id,
            'completion_percentage' => $allTasks > 0
                ? round(($completedCount / $allTasks) * 100)
                : 0,
            'last_activity' => optional($this->tasks()->latest('updated_at')->first())->updated_at?->timestamp,
        ];
    }
}
