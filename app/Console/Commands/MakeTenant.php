<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

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
        $tenant = null;

        while (true) {
            try {
                $name = text(
                    label: 'Tenant name?',
                    required: true,
                );

                $tenant = Tenant::create([
                    'name' => $name,
                ]);

                break;
            } catch (ValidationException $e) {
                warning($e->getMessage());
            }
        }

        $this->info("Tenant '{$tenant?->name}' created.");
    }
}
