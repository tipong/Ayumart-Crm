#!/usr/bin/env php
<?php

namespace App\Commands;

use Illuminate\Console\Command;
use App\Services\FonnteService;

class TestFonnteCommand extends Command
{
    protected $signature = 'test:fonte';
    protected $description = 'Test Fonnte Service';

    public function handle()
    {
        $this->info('Testing FonnteService...');

        try {
            $fonte = app(FonnteService::class);

            $this->info('✓ FonnteService instantiated');
            $this->line("  - isConfigured: " . ($fonte->isConfigured() ? 'YES' : 'NO'));
            $this->line("  - getContactsCount: " . $fonte->getContactsCount());

        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
        }
    }
}
