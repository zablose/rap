<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zablose\Rap\Models\Role;
use Zablose\Rap\RapServiceProvider;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('config:clear');
    }

    #[Test]
    public function is_publishable(): void
    {
        $config = config_path('rap.php');

        if (File::exists($config)) {
            File::delete($config);
        }

        $this->artisan('vendor:publish', ['--provider' => RapServiceProvider::class, '--tag' => 'config']);

        $this->assertTrue(File::exists($config));
    }

    #[Test]
    public function is_readable(): void
    {
        $this->assertEquals(Role::class, config('rap.models.role'));
    }
}
