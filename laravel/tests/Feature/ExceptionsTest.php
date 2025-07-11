<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Zablose\Rap\Exceptions\PermissionDeniedException;
use Zablose\Rap\Exceptions\RoleDeniedException;

class ExceptionsTest extends TestCase
{
    #[Test]
    public function throw_role_denied_exception(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(RoleDeniedException::class);

        $this->get('/user');
    }

    #[Test]
    public function throw_permission_denied_exception(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(PermissionDeniedException::class);

        $this->post('/password/update');
    }
}
