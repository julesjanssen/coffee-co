<?php

declare(strict_types=1);

namespace App\Support\Admin;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class NavigationItem implements Arrayable
{
    public function __construct(
        private array $properties,
        private Request $request
    ) {}

    public function getTitle()
    {
        return Arr::get($this->properties, 'title');
    }

    public function getRouteName()
    {
        return Arr::get($this->properties, 'route');
    }

    public function getRoute()
    {
        $routeName = $this->getRouteName();

        return $this->isUrl()
            ? $routeName
            : route($routeName);
    }

    public function getTarget()
    {
        return $this->isUrl()
            ? '_blank'
            : '_self';
    }

    public function getIcon()
    {
        return Arr::get($this->properties, 'icon');
    }

    public function hasIcon()
    {
        return ! empty($this->getIcon());
    }

    public function isUrl()
    {
        return filter_var($this->getRouteName(), FILTER_VALIDATE_URL);
    }

    public function isActive()
    {
        $route = $this->request->route();
        if (empty($route)) {
            return false;
        }

        if ($route->getName() === $this->getRouteName()) {
            return true;
        }

        $routeRoot = Str::replaceLast('.index', '', $this->getRouteName());
        if (Route::is($routeRoot . '*')) {
            // this matches the /[route]/{slug} pattern
            return true;
        }

        return false;
    }

    public function toArray()
    {
        return [
            'active' => $this->isActive(),
            'href' => $this->getRoute(),
            'target' => $this->getTarget(),
            'hasIcon' => $this->hasIcon(),
            'icon' => $this->getIcon(),
            'title' => $this->getTitle(),
        ];
    }
}
