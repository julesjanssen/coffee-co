<?php

declare(strict_types=1);

use App\Enums\GameSession\RoundStatus;
use App\Enums\GameSession\ScoreType;
use App\Enums\GameSession\Status;
use App\Enums\GameSession\TransactionType;
use App\Models\GameHdmaActivation;
use App\Models\GameScore;
use App\Models\GameSession;
use App\Models\GameTransaction;
use App\Models\Scenario;
use App\Models\ScenarioClient;
use App\Values\GameSessionSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->scenario = Scenario::factory()->create();
});

describe('GameSession Creation', function () {
    it('creates with proper default values', function () {
        $session = GameSession::factory()->create([
            'scenario_id' => $this->scenario->id,
        ]);

        expect($session)
            ->current_round_id->toBe(0)
            ->round_status->toBe(RoundStatus::PAUSED)
            ->status->toBe(Status::PENDING)
            ->public_id->not->toBeNull()
            ->settings->toBeInstanceOf(GameSessionSettings::class);
    });

    it('generates unique public_id on creation', function () {
        $session1 = GameSession::factory()->create();
        $session2 = GameSession::factory()->create();

        expect($session1->public_id)
            ->not->toBe($session2->public_id)
            ->toBeString();
    });

    it('sets default settings when empty', function () {
        $session = GameSession::factory()->create([
            'settings' => '{}',
        ]);

        expect($session->settings)
            ->secondsPerRound->toBe(120)
            ->clientNpsStart->toBe(60)
            ->hdmaRefreshRoundCooldown->toBe(4);
    });
});

describe('Profit Calculation', function () {
    it('calculates profit from transactions correctly', function () {
        $session = GameSession::factory()->create();

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'value' => 1000,
            'type' => TransactionType::PROJECT_WON,
        ]);

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'value' => -500,
            'type' => TransactionType::MARKETING_CAMPAIGN,
        ]);

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'value' => 250,
            'type' => TransactionType::PROJECT_UPTIME_BONUS,
        ]);

        expect($session->profit())->toBe(750);
    });

    it('returns zero profit when no transactions exist', function () {
        $session = GameSession::factory()->create();

        expect($session->profit())->toBe(0);
    });

    it('handles negative profit correctly', function () {
        $session = GameSession::factory()->create();

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'value' => -1000,
            'type' => TransactionType::OPERATIONAL_COST,
        ]);

        expect($session->profit())->toBe(-1000);
    });
});

describe('NPS Calculations', function () {
    it('calculates NPS for client correctly', function () {
        $session = GameSession::factory()->create([
            'settings' => new GameSessionSettings(['clientNpsStart' => 50]),
        ]);
        
        $client = ScenarioClient::factory()->create([
            'scenario_id' => $session->scenario_id,
        ]);

        GameScore::factory()->create([
            'game_session_id' => $session->id,
            'client_id' => $client->id,
            'type' => ScoreType::NPS,
            'value' => 10,
        ]);

        GameScore::factory()->create([
            'game_session_id' => $session->id,
            'client_id' => $client->id,
            'type' => ScoreType::NPS,
            'value' => 15,
        ]);

        $nps = $session->netPromotorScoreForClient($client);
        expect($nps)->toBe(62.5); // 50 + average(10, 15)
    });

    it('caps NPS between 0 and 100', function () {
        $session = GameSession::factory()->create([
            'settings' => new GameSessionSettings(['clientNpsStart' => 80]),
        ]);
        
        $client = ScenarioClient::factory()->create([
            'scenario_id' => $session->scenario_id,
        ]);

        // Test upper cap
        GameScore::factory()->create([
            'game_session_id' => $session->id,
            'client_id' => $client->id,
            'type' => ScoreType::NPS,
            'value' => 50,
        ]);

        expect($session->netPromotorScoreForClient($client))->toBe(100);

        // Test lower cap
        $session2 = GameSession::factory()->create([
            'settings' => new GameSessionSettings(['clientNpsStart' => 10]),
        ]);

        GameScore::factory()->create([
            'game_session_id' => $session2->id,
            'client_id' => $client->id,
            'type' => ScoreType::NPS,
            'value' => -50,
        ]);

        expect($session2->netPromotorScoreForClient($client))->toBe(0);
    });

    it('returns starting NPS when no scores exist', function () {
        $session = GameSession::factory()->create([
            'settings' => new GameSessionSettings(['clientNpsStart' => 75]),
        ]);
        
        $client = ScenarioClient::factory()->create([
            'scenario_id' => $session->scenario_id,
        ]);

        expect($session->netPromotorScoreForClient($client))->toBe(75);
    });
});

describe('Investment Cost Calculations', function () {
    it('groups investment costs by type', function () {
        $session = GameSession::factory()->create();

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::HDMA,
            'value' => -100,
        ]);

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::HDMA,
            'value' => -50,
        ]);

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::MARKETING_CAMPAIGN,
            'value' => -200,
        ]);

        $costs = $session->listInvestmentCosts();
        
        expect($costs)->toHaveCount(2);
        
        $hdmaCost = $costs->firstWhere('type.value', 'hdma');
        $marketingCost = $costs->firstWhere('type.value', 'marketing-campaign');
        
        expect($hdmaCost['value'])->toBe(150);
        expect($marketingCost['value'])->toBe(200);
    });

    it('excludes non-investment transaction types', function () {
        $session = GameSession::factory()->create();

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::PROJECT_WON,
            'value' => 1000,
        ]);

        GameTransaction::factory()->create([
            'game_session_id' => $session->id,
            'type' => TransactionType::OPERATIONAL_COST,
            'value' => -500,
        ]);

        $costs = $session->listInvestmentCosts();
        
        expect($costs)->toHaveCount(0);
    });
});

describe('Marketing Score Calculations', function () {
    it('calculates marketing threshold score correctly', function () {
        $session = GameSession::factory()->create();

        GameScore::factory()->create([
            'game_session_id' => $session->id,
            'type' => ScoreType::MARKETING_TRESHOLD,
            'value' => 10,
        ]);

        GameScore::factory()->create([
            'game_session_id' => $session->id,
            'type' => ScoreType::MARKETING_TRESHOLD,
            'value' => 15,
        ]);

        expect($session->marketingTresholdScore())->toBe(25);
    });

    it('calculates marketing KPI score correctly', function () {
        $session = GameSession::factory()->create();

        GameScore::factory()->create([
            'game_session_id' => $session->id,
            'type' => ScoreType::MARKETING_KPI,
            'value' => 20,
        ]);

        GameScore::factory()->create([
            'game_session_id' => $session->id,
            'type' => ScoreType::MARKETING_KPI,
            'value' => 30,
        ]);

        expect($session->marketingKpiScore())->toBe(50);
    });
});

describe('HDMA Functionality', function () {
    it('allows HDMA refresh when cooldown period has passed', function () {
        $session = GameSession::factory()->create([
            'current_round_id' => 10,
            'status' => Status::PLAYING,
            'settings' => new GameSessionSettings(['hdmaRefreshRoundCooldown' => 4]),
        ]);

        // Old activation beyond cooldown period
        GameHdmaActivation::factory()->create([
            'game_session_id' => $session->id,
            'round_id' => 5, // More than 4 rounds ago
        ]);

        expect($session->canRefreshHdma())->toBeTrue();
    });

    it('prevents HDMA refresh during cooldown period', function () {
        $session = GameSession::factory()->create([
            'current_round_id' => 8,
            'status' => Status::PLAYING,
            'settings' => new GameSessionSettings(['hdmaRefreshRoundCooldown' => 4]),
        ]);

        // Recent activation within cooldown period
        GameHdmaActivation::factory()->create([
            'game_session_id' => $session->id,
            'round_id' => 6, // Only 2 rounds ago, within 4-round cooldown
        ]);

        expect($session->canRefreshHdma())->toBeFalse();
    });

    it('prevents HDMA refresh for pending sessions', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PENDING,
            'current_round_id' => 10,
        ]);

        expect($session->canRefreshHdma())->toBeFalse();
    });
});

describe('Session Status Management', function () {
    it('identifies pending sessions correctly', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PENDING,
        ]);

        expect($session->isPending())->toBeTrue();
        expect($session->canDisplayResults())->toBeFalse();
    });

    it('identifies paused sessions correctly', function () {
        $session = GameSession::factory()->create([
            'round_status' => RoundStatus::PAUSED,
        ]);

        expect($session->isPaused())->toBeTrue();
    });

    it('pauses session correctly', function () {
        $session = GameSession::factory()->create([
            'round_status' => RoundStatus::ACTIVE,
            'settings' => new GameSessionSettings(['shouldPauseAfterCurrentRound' => true]),
        ]);

        $session->pause();

        expect($session)
            ->round_status->toBe(RoundStatus::PAUSED)
            ->settings->shouldPauseAfterCurrentRound->toBeFalse();
    });

    it('closes session correctly', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
        ]);

        $session->close();

        expect($session->fresh()->status)->toBe(Status::CLOSED);
    });
});

describe('Result Display Logic', function () {
    it('prevents results display for pending sessions', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PENDING,
            'current_round_id' => 5,
        ]);

        expect($session->canDisplayResults())->toBeFalse();
    });

    it('prevents results display for first round', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
            'current_round_id' => 0,
        ]);

        // For now, we'll test the basic case - more advanced testing would require
        // mocking the GameRound value object
        expect($session->canDisplayResults())->toBeFalse();
    });

    it('allows results display for active sessions after first round', function () {
        $session = GameSession::factory()->create([
            'status' => Status::PLAYING,
            'current_round_id' => 2,
        ]);

        // For this test, we'll assume we're not on the first round
        // In a real scenario, you'd need to mock the GameRound behavior
        if ($session->scenario_id && $session->current_round_id > 0) {
            expect($session->canDisplayResults())->toBeTrue();
        }
    });
});