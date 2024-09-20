<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\TenantCreate;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

class MakeTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = text(
            label: 'Tenant name?',
            required: true,
        );

        TenantCreate::run($name);

        $this->info("Tenant '{$name}' created.");
    }
}
