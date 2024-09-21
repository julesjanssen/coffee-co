<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;

use function Laravel\Prompts\search;
use function Laravel\Prompts\text;

class MakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user.';

    /**
     * Execute the console command.
     */
    public function handle(CreatesNewUsers $creator)
    {
        $data = $this->data();
        $tenant = $this->getTenant();

        $tenant->makeCurrent();
        $creator->create($data);

        $this->info('Created user "' . $data['email'] . '" with password "' . $data['password'] . '".');

        return Command::SUCCESS;
    }

    private function getTenant(): Tenant
    {
        $tenantID = search(
            label: 'For which tenant?',
            options: fn(string $value) => strlen($value) > 0
                ? Tenant::where('name', 'LIKE', $value . '%')->limit(10)->pluck('name', 'id')->all()
                : [],
        );

        return Tenant::find($tenantID);
    }

    private function data(): array
    {
        $name = text(
            label: 'Name',
            required: true,
        );

        $email = text(
            label: 'Email',
            required: true,
            validate: fn(string $v) => filter_var($v, FILTER_VALIDATE_EMAIL)
                    ? null
                    : 'Please fill out a valid email address',
        );

        $password = collect([1, 2, 3, 4])->map(fn() => Str::random(4))->join('-');

        return [
            'name' => $name,
            'email' => Str::lower($email),
            'password' => $password,
            'password_confirmation' => $password,
        ];
    }
}
