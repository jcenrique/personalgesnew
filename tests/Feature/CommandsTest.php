<?php

namespace Tests\Feature;

use Tests\TestCase;

class CommandsTest extends TestCase
{
    public function test_check_reconocimientos_command_runs_successfully(): void
    {
        $this->artisan('app:check-reconocimientos --help')
            ->assertExitCode(0);
    }

    public function test_migrate_old_data_command_runs_successfully(): void
    {
        $this->artisan('app:migrate-old-data --help')
            ->assertExitCode(0);
    }
}