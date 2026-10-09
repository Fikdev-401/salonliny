<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class CleanupDatabase extends Command
{
    protected $signature = 'db:cleanup {--force : Force the operation without confirmation}';
    
    protected $description = 'Clean up database for production - keep only superadmin and essential data';

    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  PERINGATAN: Command ini akan menghapus SEMUA data kecuali:');
            $this->warn('   - Akun Super Admin');
            $this->warn('   - Data Role');
            $this->warn('   - Data Cabang (hanya 1 default)');
            $this->newLine();
            $this->warn('Gunakan --force untuk melewati konfirmasi');
            $this->info('Contoh: php artisan db:cleanup --force');
            $this->newLine();
            return 1;
        }

        $this->info('🧹 Starting database cleanup...');
        $this->newLine();

        try {
            $superAdminRole = Role::where('slug', 'super-admin')->first();
            $superAdmin = $superAdminRole
                ? User::where('role_id', $superAdminRole->id)->first()
                : null;

            if ($superAdmin) {
                $this->info("✓ Super Admin found: {$superAdmin->nama}");
            } else {
                $this->warn('⚠️  Super Admin belum ada, cleanup dilanjutkan untuk fresh setup.');
            }

            // Tables to truncate (in order to respect foreign keys)
            $tables = [
                'transaksi_details',
                'transaksis',
                'pembelians',
                'pengeluarans',
                'absensis',
                'gajis',
                'karyawans',
                'pelanggans',
                'layanans',
                'produks',
                'konfigurasis',
            ];

            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->delete();
                    $this->info("  ✓ Cleared table: {$table}");
                }
            }

            // Delete users except super admin
            $deletedUsers = User::query()
                ->when($superAdmin, fn ($query) => $query->where('id', '!=', $superAdmin->id))
                ->delete();
            $this->info("  ✓ Deleted {$deletedUsers} user(s)");

            // Keep one active default cabang
            $cabangTable = 'cabangs';
            if (DB::getSchemaBuilder()->hasTable($cabangTable)) {
                $defaultCabang = DB::table($cabangTable)->where('is_default', true)->first();

                if (!$defaultCabang) {
                    $defaultCabang = DB::table($cabangTable)->orderBy('id')->first();
                }

                if ($defaultCabang) {
                    DB::table($cabangTable)
                        ->where('id', $defaultCabang->id)
                        ->update([
                            'is_default' => true,
                            'status' => 'aktif',
                            'updated_at' => now(),
                        ]);

                    $deletedCabang = DB::table($cabangTable)
                        ->where('id', '!=', $defaultCabang->id)
                        ->delete();
                    $this->info("  ✓ Deleted {$deletedCabang} cabang(s)");
                } else {
                    $defaultCabangId = DB::table($cabangTable)->insertGetId([
                        'nama' => 'Cabang Utama',
                        'alamat' => 'Alamat Cabang Utama',
                        'status' => 'aktif',
                        'is_default' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->info("  ✓ Created default cabang (ID: {$defaultCabangId})");
                }
            }

            // Reset auto increments (database specific)
            $driver = DB::getDriverName();
            $sequenceTables = array_unique(array_merge($tables, ['users', 'cabangs']));
            if ($driver === 'sqlite') {
                // For SQLite - check if sqlite_sequence table exists
                if (DB::getSchemaBuilder()->hasTable('sqlite_sequence')) {
                    foreach ($sequenceTables as $table) {
                        if (DB::getSchemaBuilder()->hasTable($table)) {
                            DB::table('sqlite_sequence')->where('name', $table)->delete();
                        }
                    }
                }
            } elseif ($driver === 'mysql') {
                // For MySQL
                foreach ($sequenceTables as $table) {
                    if (DB::getSchemaBuilder()->hasTable($table)) {
                        DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
                    }
                }
            }

            $this->newLine();
            $this->info('✅ Database cleanup completed successfully!');
            if ($superAdmin) {
                $this->info("   Super Admin: {$superAdmin->username} ({$superAdmin->email})");
                $this->info("   Password: [unchanged]");
            } else {
                $this->info('   Super Admin: belum tersedia (akan dibuat oleh ProductionSeeder)');
            }
            $this->newLine();
            $this->info('🚀 Database is now ready for production!');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error during cleanup: ' . $e->getMessage());
            return 1;
        }
    }
}
