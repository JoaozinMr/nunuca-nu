<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Prevent "Vite manifest not found" during tests.
        // Assets are not needed for HTTP/feature tests.
        $this->withoutVite();
    }
}
