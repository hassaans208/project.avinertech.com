<?php

namespace App\Jobs;

use App\Services\BatchExecutionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $groupId;
    public int $timeout = 300; // 5 minutes timeout
    public int $tries = 3;

    public function __construct(int $groupId)
    {
        $this->groupId = $groupId;
    }

    public function handle(BatchExecutionService $batchExecutionService): void
    {
        try {
            Log::info('Starting batch processing job', [
                'group_id' => $this->groupId,
                'job_id' => $this->job->getJobId()
            ]);

            $result = $batchExecutionService->executeBatch($this->groupId);

            Log::info('Batch processing job completed successfully', [
                'group_id' => $this->groupId,
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Batch processing job failed', [
                'group_id' => $this->groupId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw the exception to mark the job as failed
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Batch processing job permanently failed', [
            'group_id' => $this->groupId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Update group status to failed
        \DB::table('operation_groups')
            ->where('id', $this->groupId)
            ->update([
                'status' => 'failed',
                'completed_at' => now(),
                'updated_at' => now()
            ]);
    }
}
