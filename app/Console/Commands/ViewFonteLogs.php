<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ViewFonteLogs extends Command
{
    protected $signature = 'fonte:logs {--tail=50} {--search=} {--error-only} {--live}';
    protected $description = 'View Fonte-related logs from laravel.log';

    public function handle()
    {
        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->line('         FONTE LOGS VIEWER');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->line('');

        $logPath = storage_path('logs/laravel.log');

        if (!file_exists($logPath)) {
            $this->error('❌ Log file not found: ' . $logPath);
            return 1;
        }

        $tail = $this->option('tail') ?? 50;
        $search = $this->option('search') ?? 'fonte';
        $errorOnly = $this->option('error-only');
        $live = $this->option('live');

        $this->line("📂 Log file: {$logPath}");
        $this->line("📊 Displaying last {$tail} lines");
        if ($errorOnly) {
            $this->line("🔴 Filter: Errors only");
        } else {
            $this->line("🔍 Filter: '{$search}'");
        }
        $this->line('');

        if ($live) {
            $this->viewLive($logPath, $search, $errorOnly);
        } else {
            $this->viewStatic($logPath, $search, $errorOnly, $tail);
        }

        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->line('');

        return 0;
    }

    private function viewStatic($logPath, $search, $errorOnly, $tail)
    {
        $command = "tail -n {$tail} {$logPath}";

        if ($errorOnly) {
            $command .= " | grep -i 'error\\|exception\\|failed'";
        } else {
            $command .= " | grep -i '{$search}'";
        }

        $output = shell_exec($command);

        if (!$output) {
            $this->warn("⚠️ No logs found matching your criteria");
            return;
        }

        // Parse and display logs with colors
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }

            // Parse log format: [date] channel.level: message
            if (preg_match('/\[(.*?)\]\s+(\w+)\.(\w+):\s+(.*)/i', $line, $matches)) {
                $timestamp = $matches[1];
                $channel = $matches[2];
                $level = strtoupper($matches[3]);
                $message = $matches[4];

                // Color based on level
                $levelTag = match ($level) {
                    'ERROR' => '<fg=red;options=bold>[ERROR]</>',
                    'WARNING' => '<fg=yellow>[WARNING]</>',
                    'INFO' => '<fg=green>[INFO]</>',
                    'DEBUG' => '<fg=cyan>[DEBUG]</>',
                    default => "[{$level}]"
                };

                $this->line("{$timestamp} {$levelTag} {$message}");
            } else {
                $this->line($line);
            }
        }
    }

    private function viewLive($logPath, $search, $errorOnly)
    {
        $this->info('🔴 LIVE MODE - Press Ctrl+C to exit');
        $this->line('');

        $command = "tail -f {$logPath}";

        if ($errorOnly) {
            $command .= " | grep -i 'error\\|exception\\|failed'";
        } else {
            $command .= " | grep -i '{$search}'";
        }

        passthru($command);
    }
}
