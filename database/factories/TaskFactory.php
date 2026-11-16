<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => 'Task '.bin2hex(random_bytes(4)),
            'description' => 'Prepare the counter for the next working day.',
            'due_date' => today()->toDateString(),
            'priority' => 'medium',
            'status' => 'pending',
            'is_archived' => false,
        ];
    }
}
