<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    // method ini running ketika test di mulai
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete("DELETE FROM addresses");
        DB::delete("DELETE FROM contacts");
        DB::delete("DELETE FROM users");
    }
}
