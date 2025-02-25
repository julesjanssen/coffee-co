<?php

declare(strict_types=1);

namespace App\Listeners\Tenant;

use App\Events\TenantCreating;
use App\Models\Tenant;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EnsureValidNameAndSlug
{
    public function handle(TenantCreating $event)
    {
        /** @var Tenant $tenant */
        $tenant = $event->tenant;

        if (empty($tenant->slug)) {
            $tenant->slug = Str::slug($tenant->name);
        }

        $this->validateLengthAndUniqueness($tenant);
        $this->validateSlugForSubdomain($tenant);
    }

    private function validateLengthAndUniqueness(Tenant $tenant)
    {
        if (empty($tenant->slug)) {
            throw ValidationException::withMessages([
                'name' => ['Please enter a valid name.'],
            ]);
        }

        $tenant->slug = mb_substr((string) $tenant->slug, 0, 30);

        $exists = Tenant::query()
            ->where('slug', '=', $tenant->slug)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => ["Tenant with slug '{$tenant->slug}' already exists."],
            ]);
        }
    }

    private function validateSlugForSubdomain(Tenant $tenant)
    {
        if ($this->getSubdomainBlocklist()->contains($tenant->slug)) {
            throw ValidationException::withMessages([
                'name' => ["The tenant name '{$tenant->name}' is not allowed."],
            ]);
        }
    }

    private function getSubdomainBlocklist()
    {
        return LazyCollection::make(function () {
            $path = resource_path('config/subdomain-blocklist.txt');
            $handle = fopen($path, 'r');

            while (($line = fgets($handle)) !== false) {
                yield trim($line);
            }
        });
    }
}
