<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $samples = [
            ['title' => 'Open the counter', 'description' => 'Check the register and prepare the opening float.', 'priority' => 'high', 'status' => 'pending', 'offset' => 0],
            ['title' => 'Review low-stock items', 'description' => 'List products that need a supplier order.', 'priority' => 'high', 'status' => 'in_progress', 'offset' => 0],
            ['title' => 'Update customer records', 'description' => 'Review the sample customer directory for duplicate records.', 'priority' => 'medium', 'status' => 'pending', 'offset' => 1],
            ['title' => 'Reconcile yesterday’s sales', 'description' => 'Compare the register total with the end-of-day report.', 'priority' => 'medium', 'status' => 'done', 'offset' => 0],
            ['title' => 'Prepare the weekly inventory count', 'description' => 'Print count sheets and assign store sections.', 'priority' => 'low', 'status' => 'pending', 'offset' => 3],
        ];

        // Do not overwrite task edits or duplicate samples when re-seeding.
        foreach ($samples as $index => $sample) {
            $seedId = 3001 + $index;
            if (Task::whereKey($seedId)->exists()) {
                continue;
            }
            $task = new Task([
                'title' => $sample['title'],
                'description' => $sample['description'],
                'priority' => $sample['priority'],
                'status' => $sample['status'],
                'due_date' => today()->addDays($sample['offset'])->toDateString(),
            ]);
            $task->id = $seedId;
            $task->created_by = User::orderBy('id')->value('id');
            $task->completed_at = $sample['status'] === 'done' ? now() : null;
            $task->save();
        }
    }
}
