<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Client\CarBrand;
use App\Enums\Client\Market;
use App\Enums\Client\Segment;
use App\Enums\Client\YearsInBusiness;
use App\Enums\Locale;
use App\Enums\Product\Color;
use App\Enums\Product\Material;
use App\Enums\Product\Type;
use App\Enums\Request\CompetitionLevel;
use App\Enums\Scenario\CampaignCodeCategory;
use App\Enums\Scenario\Status;
use App\Models\Scenario;
use App\Models\ScenarioCampaignCode;
use App\Models\ScenarioClient;
use App\Models\ScenarioProduct;
use App\Models\ScenarioRequest;
use App\Models\ScenarioRequestSolution;
use App\Models\ScenarioTip;
use App\Models\Tenant;
use App\Values\ScenarioSettings;
use BackedEnum;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppMigrateScenario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-scenario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    private Locale $locale = Locale::EN;

    private array $clientIdMap = [];

    private array $productIdMap = [];

    private array $requestIdMap = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Tenant::find(1)->makeCurrent();

        $scenario = Scenario::create([
            'group_id' => $this->locale->is(Locale::EN) ? 1 : 2,
            'title' => 'Original',
            'locale' => $this->locale,
            'settings' => ScenarioSettings::fromArray([]),
            'status' => Status::ACTIVE,
        ]);

        $this->clientIdMap = [];
        $this->productIdMap = [];
        $this->requestIdMap = [];

        $this->migrateClients($scenario);
        $this->migrateProducts($scenario);
        $this->migrateRequests($scenario);
        $this->migrateSolutions($scenario);
        $this->migrateCodes($scenario);
        $this->migrateTips($scenario);
    }

    private function migrateClients(Scenario $scenario)
    {
        $this->oldDB()
            ->table('clients')
            ->get()
            ->each(fn($v) => $this->migrateClient($scenario, $v));
    }

    private function migrateClient(Scenario $scenario, object $record)
    {
        $segment = Segment::from($record->segment);

        $client = ScenarioClient::create([
            'scenario_id' => $scenario->id,
            'player_id' => $record->player_id,
            'title' => $record->title,
            'segment' => $segment,
            'settings' => [
                'carbrand' => $this->findClientDetail(CarBrand::class, $record, 1),
                'market' => $this->findClientDetail(Market::class, $record, 0),
                'years' => $this->findClientDetail(YearsInBusiness::class, $record, 2),
            ],
            'sortorder' => 1,
        ]);

        $this->clientIdMap[$record->id] = $client->id;
    }

    /**
     * @param class-string<BackedEnum> $enum
     */
    private function findClientDetail(string $enum, object $record, int $index)
    {
        /** @phpstan-ignore-next-line */
        $cases = $enum::collect();

        $info = json_decode($record->info, true)['en']['general'];
        $statement = Str::ascii(Str::lower($info[$index]));

        foreach ($cases as $case) {
            $value = $case->value;
            if ($case instanceof YearsInBusiness) {
                $value = $case->years();
            } else {
                $value = str_replace('-', ' ', $value);
            }

            if (Str::contains($statement, $value)) {
                return $case;
            }
        }

        throw new Exception('Details not found for ' . $statement);
    }

    private function migrateProducts(Scenario $scenario)
    {
        $this->oldDB()
            ->table('products')
            ->where('id', '<=', 800)
            ->get()
            ->each(fn($v) => $this->migrateProduct($scenario, $v));
    }

    private function migrateProduct(Scenario $scenario, object $record)
    {
        if ($record->material === 'aluminum') {
            $record->material = Material::ALUMINIUM->value;
        }

        $type = Str::replace('-and-', '-', Str::slug($record->description));
        $type = Type::from($type);

        $material = empty($record->material) ? null : Material::from($record->material);
        $color = empty($record->color) ? null : Color::from($record->color);

        $product = ScenarioProduct::create([
            'scenario_id' => $scenario->id,
            'public_id' => $record->external_id,
            'type' => $type,
            'material' => $material,
            'color' => $color,
        ]);

        $this->productIdMap[$record->id] = $product->id;
    }

    private function migrateRequests(Scenario $scenario)
    {
        $this->oldDB()
            ->table('requests')
            ->get()
            ->each(fn($v) => $this->migrateRequest($scenario, $v));
    }

    private function migrateRequest(Scenario $scenario, object $record)
    {
        $clientID = $this->clientIdMap[$record->client_id];
        $description = json_decode($record->description, true);
        $requirements = collect(json_decode($record->requirements, true))
            ->mapWithKeys(fn($v, $k) => [Str::camel($k) => $v])
            ->toArray();
        $data = collect(json_decode($record->data, true))
            ->mapWithKeys(fn($v, $k) => [Str::camel($k) => $v])
            ->toArray();

        $labConsultingInformation = Arr::get($data, 'info.en.labconsulting');
        if ($labConsultingInformation === 'There is no information we can help you with. Try again later.') {
            $labConsultingInformation = null;
        }
        $data['labconsultinginformation'] = $labConsultingInformation;

        $competitionLevel = Str::lower(Arr::get($data, 'info.en.deallost') ?? CompetitionLevel::MEDIUM->value);
        if ($competitionLevel === 'moderate') {
            $competitionLevel = CompetitionLevel::MEDIUM->value;
        }
        $data['competitionlevel'] = CompetitionLevel::from($competitionLevel);

        Arr::forget($data, 'info');

        $request = ScenarioRequest::create([
            'scenario_id' => $scenario->id,
            'client_id' => $clientID,
            'description' => $description[$this->locale->value],
            'delay' => $record->delay,
            'duration' => $record->duration,
            'requirements' => $requirements,
            'settings' => array_filter($data),
        ]);

        $this->requestIdMap[$record->id] = $request->id;
    }

    private function migrateSolutions(Scenario $scenario)
    {
        $this->oldDB()
            ->table('solutions')
            ->get()
            ->each(fn($v) => $this->migrateSolution($scenario, $v));
    }

    private function migrateSolution(Scenario $scenario, object $record)
    {
        $requestID = $this->requestIdMap[$record->request_id];
        $productIDs = collect(explode('|', $record->product_ids))
            ->map(fn($v) => $this->productIdMap[$v])
            ->sort()
            ->toArray();

        $info = json_decode($record->info, true);
        $isOptimal = ! str_contains((string) $info['en']['dealwon'], 'you did not offer');

        ScenarioRequestSolution::create([
            'scenario_id' => $scenario->id,
            'request_id' => $requestID,
            'product_ids' => $productIDs,
            'score' => $record->customerdecisionpoints,
            'is_optimal' => $isOptimal,
        ]);
    }

    private function migrateCodes(Scenario $scenario)
    {
        $this->oldDB()
            ->table('campaigncodes')
            ->get()
            ->each(fn($v) => $this->migrateCode($scenario, $v));
    }

    private function migrateCode(Scenario $scenario, object $record)
    {
        ScenarioCampaignCode::create([
            'scenario_id' => $scenario->id,
            'code' => $record->code,
            'category' => CampaignCodeCategory::from($record->type),
        ]);
    }

    private function migrateTips(Scenario $scenario)
    {
        $this->oldDB()
            ->table('tips')
            ->get()
            ->each(fn($v) => $this->migrateTip($scenario, $v));
    }

    private function migrateTip(Scenario $scenario, object $record)
    {
        $text = json_decode($record->text, true);

        ScenarioTip::create([
            'scenario_id' => $scenario->id,
            'content' => $text[$this->locale->value],
        ]);
    }

    protected function oldDB()
    {
        return once(fn() => DB::connectUsing('old', [
            ...config('database.connections.landlord'),
            'database' => 'incontext-roche-generic',
            'prefix' => '',
        ]));
    }
}
