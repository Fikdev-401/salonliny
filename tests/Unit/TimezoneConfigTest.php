<?php

namespace Tests\Unit;

use Tests\TestCase;

class TimezoneConfigTest extends TestCase
{
    public function test_application_timezone_is_asia_makassar(): void
    {
        $this->assertSame('Asia/Makassar', config('app.timezone'));
        $this->assertSame('Asia/Makassar', now()->timezoneName);
    }
}
