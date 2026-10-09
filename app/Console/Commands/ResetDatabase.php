<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetDatabase extends Command
{
    protected $signature = 'db:reset {--force : Force without confirmation}';
    
    protected $description = 'Drop all tables and recreate with fresh migrations and seeds';

    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  PERINGATAN: Command ini akan:');
            $this->warn('   - Menghapus SEMUA tabel di database');
            $this->warn('   - Membuat ulang tabel dari migrasi');
            $this->warn('   - Seed data esensial (superadmin, roles, cabang)');
            $this->newLine();
            $this->info('Gunakan --force untuk melewati konfirmasi');
            $this->info('Contoh: php artisan db:reset --force');
            return 1;
        }

        $this->info('🗑️  Resetting schema with migrate:fresh...');
        $freshCode = $this->call('migrate:fresh', ['--force' => true]);
        if ($freshCode !== 0) {
            $this->error('❌ Failed to reset schema.');
            return 1;
        }

        $this->newLine();
        $this->info('🌱 Seeding data...');
        $seedCode = $this->call('db:seed', ['--class' => 'ProductionSeeder', '--force' => true]);
        if ($seedCode !== 0) {
            $this->error('❌ Failed to seed production data.');
            return 1;
        }

        $this->newLine();
        $this->info('✅ Database reset completed!');
        $this->newLine();
        $this->info('🔑 Default login:');
        $this->info('   Username: superadmin');
        $this->info('   Password: password');

        return 0;
    }
}
