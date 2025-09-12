<?php

declare(strict_types=1);

use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\Status;
use App\Enums\GameSession\TransactionType;
use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use App\Models\GameSession;
use App\Models\GameTransaction;
use App\Models\Project;
use App\Models\Scenario;
use App\Models\User;
use App\Values\GameSessionSettings;
// Note: Using TestCase which handles DatabaseMigrations for multitenancy

beforeEach(function () {
    $this->scenario = Scenario::factory()->create();
    $this->user = User::factory()->create();
});

describe('Game Session Creation Flow', function () {
    it('creates a new game session with proper initialization', function () {
        $response = $this->actingAs($this->user)
            ->post('/admin/game-sessions/create', [
                'name' => 'Test Game Session',
                'scenario_id' => $this->scenario->id,
                'settings' => [
                    'secondsPerRound' => 180,
                    'clientNpsStart' => 70,
                ],
            ]);

        $response->assertRedirect();

        $session = GameSession::latest()->first();
        
        expect($session)
            ->name->toBe('Test Game Session')
            ->scenario_id->toBe($this->scenario->id)
            ->status->toBe(Status::PENDING)
            ->round_status->toBe(RoundStatus::PAUSED)
            ->current_round_id->toBe(0)
            ->public_id->not->toBeNull();

        expect($session->settings)
            ->secondsPerRound->toBe(180)
            ->clientNpsStart->toBe(70);
    });

    it('requires valid scenario for game session creation', function () {
        $response = $this->actingAs($this->user)
            ->post('/admin/game-sessions/create', [
                'name' => 'Test Game Session',
                'scenario_id' => 99999, // Non-existent scenario
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['scenario_id']);
    });

    it('validates required fields during creation', function () {
        $response = $this->actingAs($this->user)
            ->post('/admin/game-sessions/create', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    });
});

describe('Game Session Status Management', function () {
    it('updates session status from pending to active', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PENDING,
            'scenario_id' => $this->scenario->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/admin/game-sessions/{$session->sqid}/status/update", [
                'status' => 'playing',
            ]);

        $response->assertSuccessful();
        
        expect($session->fresh()->status)->toBe(Status::PLAYING);
    });

    it('prevents invalid status transitions', function () {
        $session = GameSession::factory()->create([
            'status' => Status::CLOSED,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/admin/game-sessions/{$session->sqid}/status/update", [
                'status' => 'pending',
            ]);

        // Should validate that closed sessions can't go back to pending
        $response->assertUnprocessable();
    });

    it('closes active session', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
        ]);

        $session->close();

        expect($session->fresh()->status)->toBe(Status::CLOSED);
    });
});

describe('Round Management', function () {
    it('advances to next round when conditions are met', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
            'current_round_id' => 1,
            'round_status' => RoundStatus::PROCESSED,
        ]);

        $facilitator = GameFacilitator::factory()->create([
            'game_session_id' => $session->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post('/game/facilitator/round/status', [
                'session_id' => $session->id,
                'action' => 'next_round',
            ]);

        $response->assertSuccessful();
        
        expect($session->fresh())
            ->current_round_id->toBe(2)
            ->round_status->toBe(RoundStatus::PAUSED);
    });

    it('pauses round when requested', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
            'round_status' => RoundStatus::ACTIVE,
        ]);

        $session->pause();

        expect($session)
            ->round_status->toBe(RoundStatus::PAUSED)
            ->settings->shouldPauseAfterCurrentRound->toBeFalse();
    });

    it('processes round transitions correctly', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
            'current_round_id' => 3,
            'round_status' => RoundStatus::ACTIVE,
        ]);

        // Simulate round processing
        $session->update(['round_status' => RoundStatus::PROCESSING]);
        expect($session->round_status)->toBe(RoundStatus::PROCESSING);

        // Complete processing
        $session->update(['round_status' => RoundStatus::PROCESSED]);
        expect($session->round_status)->toBe(RoundStatus::PROCESSED);
    });
});

describe('Participant Management', function () {
    it('allows participants to join active session', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
        ]);

        $response = $this->post("/game/sessions/{$session->sqid}", [
            'name' => 'John Doe',
            'role' => 'participant',
        ]);

        $response->assertRedirect("/game/sessions/{$session->sqid}");

        expect(GameParticipant::where('game_session_id', $session->id)->count())
            ->toBe(1);

        $participant = GameParticipant::first();
        expect($participant)
            ->name->toBe('John Doe')
            ->game_session_id->toBe($session->id);
    });

    it('prevents joining closed sessions', function () {
        $session = GameSession::factory()->create([
            'status' => Status::CLOSED,
        ]);

        $response = $this->post("/game/sessions/{$session->sqid}", [
            'name' => 'John Doe',
            'role' => 'participant',
        ]);

        $response->assertForbidden();
    });

    it('tracks multiple participants in same session', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
        ]);

        GameParticipant::factory()->count(3)->create([
            'game_session_id' => $session->id,
        ]);

        expect($session->participants()->count())->toBe(3);
    });
});

describe('Project and Transaction Flow', function () {
    it('creates projects within game session context', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
        ]);

        $project = Project::factory()->create([
            'game_session_id' => $session->id,
        ]);

        expect($session->projects()->count())->toBe(1);
        expect($project->game_session_id)->toBe($session->id);
    });

    it('records financial transactions correctly', function () {
        $session = GameSession::factory()->create();

        // Project won transaction
        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::PROJECT_WON,
            'value' => 5000,
        ]);

        // Marketing investment
        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::MARKETING_CAMPAIGN,
            'value' => -1000,
        ]);

        expect($session->transactions()->count())->toBe(2);
        expect($session->profit())->toBe(4000);
    });

    it('calculates investment costs for reporting', function () {
        $session = GameSession::factory()->create();

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::HDMA,
            'value' => -500,
        ]);

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::LAB_CONSULTING,
            'value' => -250,
        ]);

        // Non-investment transaction (should be excluded)
        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::PROJECT_WON,
            'value' => 1000,
        ]);

        $costs = $session->listInvestmentCosts();
        
        expect($costs)->toHaveCount(2);
        expect($costs->sum('value'))->toBe(750);
    });
});

describe('Facilitator Access Control', function () {
    it('grants facilitator access with valid hash', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
        ]);

        $facilitator = GameFacilitator::factory()->create([
            'game_session_id' => $session->id,
        ]);

        $response = $this->get("/game/sessions/{$session->sqid}/f/{$facilitator->hash}");

        $response->assertSuccessful();
        $response->assertInertia(fn($assert) => 
            $assert->component('Game/Facilitator/Dashboard')
                   ->has('session')
        );
    });

    it('denies access with invalid facilitator hash', function () {
        $session = GameSession::factory()->create();

        $response = $this->get("/game/sessions/{$session->sqid}/f/invalid-hash");

        $response->assertNotFound();
    });

    it('allows facilitator to update session settings', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
            'settings' => new GameSessionSettings(['secondsPerRound' => 120]),
        ]);

        GameFacilitator::factory()->create([
            'game_session_id' => $session->id,
        ]);

        $response = $this->post('/game/facilitator/session/settings', [
            'session_id' => $session->id,
            'seconds_per_round' => 180,
            'client_nps_start' => 75,
        ]);

        $response->assertSuccessful();

        $updatedSession = $session->fresh();
        expect($updatedSession->settings)
            ->secondsPerRound->toBe(180)
            ->clientNpsStart->toBe(75);
    });
});

describe('Session Cleanup and Finalization', function () {
    it('properly finalizes completed session', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
            'current_round_id' => 20, // Assuming this represents a completed game
        ]);

        // Add some final transactions
        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::PROJECT_UPTIME_BONUS,
            'value' => 500,
        ]);

        $session->update([
            'status' => Status::FINISHED,
            'finished_at' => now(),
        ]);

        expect($session->fresh())
            ->status->toBe(Status::FINISHED)
            ->finished_at->not->toBeNull();

        expect($session->profit())->toBeGreaterThanOrEqualTo(500);
    });

    it('maintains data integrity after session completion', function () {
        $session = GameSession::factory()->create([
            'status' => Status::FINISHED,
        ]);

        // Create related data
        $participants = GameParticipant::factory()->count(2)->create([
            'game_session_id' => $session->id,
        ]);

        $projects = Project::factory()->count(3)->create([
            'game_session_id' => $session->id,
        ]);

        // Verify relationships are maintained
        expect($session->participants()->count())->toBe(2);
        expect($session->projects()->count())->toBe(3);
        
        // Verify cascade behavior if session is soft deleted
        $session->delete();
        
        expect(GameSession::withTrashed()->find($session->id))
            ->not->toBeNull();
    });
});
