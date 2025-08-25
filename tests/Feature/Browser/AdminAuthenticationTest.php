<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('can visit the game homepage', function () {
    $page = visit('/');

    // Since / redirects to /game, we should see the game interface
    $page->assertSee('No active')->assertNoJavaScriptErrors();
});

it('redirects unauthenticated users to login', function () {
    $page = visit('/admin/accounts');

    $page->assertPathContains('/login')
        ->assertSee('Sign In')
        ->assertNoJavaScriptErrors();
});

it('can view the login page', function () {
    $page = visit('/auth/login');

    $page->assertSee('Sign In')
        ->assertPresent('[name="email"]')
        ->assertPresent('[type="submit"]')
        ->assertNoJavaScriptErrors();
});

it('shows validation errors for empty login form', function () {
    $page = visit('/auth/login');

    $page->click('[type="submit"]')
        ->wait(1)
        ->assertSee('required')
        ->assertNoJavaScriptErrors();
});

it('can authenticate with valid credentials', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);

    $page = visit('/auth/login');

    $page->fill('email', 'admin@example.com')
        ->fill('password', 'password123')
        ->click('[type="submit"]')
        ->wait(1)
        ->navigate('/admin/')
        ->assertSee('Dashboard')
        ->assertNoJavaScriptErrors();
});

it('shows error for invalid credentials', function () {
    $page = visit('/auth/login');

    $page->fill('email', 'nonexistent@example.com')
        ->fill('password', 'wrongpassword')
        ->click('[type="submit"]')
        ->wait(1)
        ->assertSee('These credentials do not match our records')
        ->assertPathContains('/login')
        ->assertNoJavaScriptErrors();
});

it('can access admin dashboard when authenticated', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $page = visit('/admin');

    $page->assertSee('Dashboard')
        ->wait(1)
        ->assertPresent('nav, navigation')
        ->assertNoJavaScriptErrors();
});

it('can navigate to accounts section', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $page = visit('/admin');

    $page->click('Accounts')
        ->assertPathIs('/admin/accounts')
        ->assertSee('Accounts')
        ->assertNoJavaScriptErrors();
});

it('can navigate to game sessions section', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $page = visit('/admin');

    $page->click('Sessions')
        ->assertPathIs('/admin/game-sessions')
        ->assertSee('Game Sessions')
        ->assertNoJavaScriptErrors();
});

it('can navigate to system/server section', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $page = visit('/admin');

    $page->click('Server')
        ->assertPathContains('/admin/system/server')
        ->assertNoJavaScriptErrors();
});

it('can logout successfully', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $page = visit('/admin');

    $page->click('.account-slide .name')
        ->wait(1)
        ->click('log out')
        ->assertPathContains('/auth/login')
        ->assertSee('sign in')
        ->assertNoJavaScriptErrors();

    $this->assertGuest();
});

it('maintains session across page navigations', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $page = visit('/admin');

    $page->click('Accounts')
        ->assertPathIs('/admin/accounts')
        ->navigate('/admin/system/server')
        ->assertSee('Server')
        ->assertNoJavaScriptErrors();
});

it('works correctly on mobile viewport', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $page = visit('/admin')->on()->mobile();

    $page->assertSee('Dashboard')->assertNoJavaScriptErrors();
});

it('can perform smoke testing across key admin pages', function () {
    $user = User::factory()->admin()->create();
    $this->actingAs($user);

    $pages = visit([
        '/admin',
        '/admin/accounts',
        '/admin/game-sessions',
        '/admin/system/server',
        '/admin/system/changelog',
    ]);

    $pages->assertNoJavaScriptErrors();

    [$dashboard, $accounts, $sessions, $server, $changelog] = $pages;

    $dashboard->assertSee('Dashboard');
    $accounts->assertSee('Accounts');
    $sessions->assertSee('Game Sessions');
    $server->assertSee('Server');
    $changelog->assertSee('Changelog');
});
