<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class LogViewerController extends Controller
{
    /**
     * Display Fonnte logs in a web interface
     */
    public function fonteLogs(): View
    {
        // Get last 100 Fonnte-related log entries
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            $output = shell_exec("grep -i 'fonnte' {$logPath} | tail -100");
            if ($output) {
                $logLines = array_reverse(explode("\n", trim($output)));
                foreach ($logLines as $line) {
                    if (empty($line)) {
                        continue;
                    }

                    // Parse log line
                    if (preg_match('/\[(.*?)\]\s+(\w+)\.(\w+):\s+(.*)/i', $line, $matches)) {
                        $logs[] = [
                            'timestamp' => $matches[1] ?? '',
                            'channel' => $matches[2] ?? '',
                            'level' => strtoupper($matches[3] ?? ''),
                            'message' => $matches[4] ?? '',
                            'raw' => $line
                        ];
                    }
                }
            }
        }

        // Parse JSON messages from logs
        $parsedLogs = [];
        foreach ($logs as $log) {
            // Try to extract JSON if message contains it
            if (preg_match('/(\{.*\})/s', $log['message'], $matches)) {
                try {
                    $json = json_decode($matches[1], true);
                    if ($json) {
                        $log['data'] = $json;
                    }
                } catch (\Exception $e) {
                    // Ignore JSON parse errors
                }
            }
            $parsedLogs[] = $log;
        }

        return view('admin.logs.fonnte', [
            'logs' => $parsedLogs,
            'total' => count($parsedLogs)
        ]);
    }

    /**
     * Get Fonnte statistics from logs
     */
    public function fonteStats()
    {
        $logPath = storage_path('logs/laravel.log');
        $stats = [
            'total_messages' => 0,
            'successful' => 0,
            'failed' => 0,
            'pending' => 0,
            'recent_errors' => [],
            'phones_sent' => [],
        ];

        if (File::exists($logPath)) {
            $output = shell_exec("grep -i 'fonnte' {$logPath}");
            if ($output) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    if (strpos($line, 'Message sent successfully') !== false) {
                        $stats['successful']++;
                    } elseif (strpos($line, 'API Error') !== false || strpos($line, 'Exception') !== false) {
                        $stats['failed']++;
                    }

                    // Extract phone numbers
                    if (preg_match('/"phone":\s*"(\d+)"/', $line, $matches)) {
                        $stats['phones_sent'][] = $matches[1];
                    }

                    // Extract errors
                    if (preg_match('/"error":\s*"([^"]+)"/', $line, $matches)) {
                        $stats['recent_errors'][] = $matches[1];
                    }
                }

                $stats['total_messages'] = $stats['successful'] + $stats['failed'];
                $stats['phones_sent'] = array_unique($stats['phones_sent']);
                $stats['recent_errors'] = array_unique(array_slice($stats['recent_errors'], -5));
            }
        }

        return response()->json($stats);
    }
}
