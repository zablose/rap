<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;
use Zablose\Rap\RapServiceProvider;

class MigrationsTest extends TestCase
{
    /** @test */
    public function are_publishable(): void
    {
        $path = database_path('migrations').DIRECTORY_SEPARATOR;

        $migrations = [
            $path.'2016_12_04_114401_create_roles_table.php',
            $path.'2016_12_04_114402_create_permissions_table.php',
            $path.'2016_12_04_114403_create_permission_role_table.php',
            $path.'2016_12_04_114404_create_role_user_table.php',
            $path.'2016_12_04_114405_create_permission_user_table.php',
        ];

        foreach ($migrations as $migration) {
            if (File::exists($migration)) {
                File::delete($migration);
            }
        }

        $this->artisan('vendor:publish', ['--provider' => RapServiceProvider::class, '--tag' => 'migrations']);

        foreach ($migrations as $migration) {
            $this->assertTrue(File::exists($migration));
        }
    }
}
