<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupProduction extends Command
{
    protected $signature = 'app:setup-production';
    
    protected $description = 'Setup application for production (cleanup + seed essential data)';

    public function handle()
    {
        $this->info('🚀 Setting up application for production...');
        $this->newLine();

        // Step 1: Ensure latest schema exists
        $this->info('Step 1: Running migrations...');
        $migrateCode = $this->call('migrate', ['--force' => true]);
        if ($migrateCode !== 0) {
            $this->error('❌ Migration failed!');
            return 1;
        }

        // Step 2: Cleanup database
        $this->newLine();
        $this->info('Step 2: Cleaning up database...');
        $cleanupCode = $this->call('db:cleanup', ['--force' => true]);
        if ($cleanupCode !== 0) {
            $this->error('❌ Database cleanup failed!');
            return 1;
        }

        // Step 3: Seed essential data
        $this->newLine();
        $this->info('Step 3: Seeding essential data...');
        $seedCode = $this->call('db:seed', ['--class' => 'ProductionSeeder', '--force' => true]);
        if ($seedCode !== 0) {
            $this->error('❌ Production seeding failed!');
            return 1;
        }

        // Step 4: Clear caches
        $this->newLine();
        $this->info('Step 4: Clearing caches...');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        $this->call('route:clear');

        $this->newLine();
        $this->info('✅ Production setup completed!');
        $this->newLine();
        $this->info('🔑 Default login credentials:');
        $this->info('   Username: superadmin');
        $this->info('   Password: password');
        $this->newLine();
        $this->warn('⚠️  IMPORTANT: Please change the default password after first login!');

        return 0;
    }
}
