<?php

declare(strict_types=1);

use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        if (app()->runningInConsole() && app()->environment('testing')) {
            return;
        }

        Tenant::create([
            'name' => 'Coffee & Co',
        ]);
    }
};
