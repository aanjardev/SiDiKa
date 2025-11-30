<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\LongRunningProcess;

class TimeoutController extends Controller
{
    /**
     * Test timeout page directly
     */
    public function testTimeout()
    {
        return view('errors.timeout');
    }

    /**
     * Simulate actual timeout error
     */
    public function simulateTimeout()
    {
        // Force timeout dengan infinite loop
        set_time_limit(2); // 2 detik untuk testing
        while (true) {
            // Infinite loop untuk trigger timeout
        }
    }

    /**
     * Handle requests that might timeout
     */
    public function handleHeavyTask(Request $request)
    {
        try {
            // Test sederhana dulu tanpa queue
            return response()->json([
                'status' => 'processing',
                'message' => 'Tugas sedang diproses di background. Silakan cek status secara berkala.',
                'job_id' => 'test-job-' . time(),
                'data' => $request->all()
            ]);
            
        } catch (\Exception $e) {
            // Fallback ke timeout page jika error
            if (str_contains($e->getMessage(), 'Maximum execution time')) {
                return response()->view('errors.timeout', [], 503);
            }
            
            return response()->json([
                'error' => 'Process failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check job status
     */
    public function checkJobStatus($jobId)
    {
        // Implementasi pengecekan status job
        return response()->json([
            'status' => 'completed',
            'message' => 'Tugas selesai diproses',
            'job_id' => $jobId
        ]);
    }
}
