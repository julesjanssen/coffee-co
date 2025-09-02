<?php

declare(strict_types=1);

namespace App\Support\SystemTasks;

use App\Enums\AttachmentType;
use App\Enums\Client\CarBrand;
use App\Enums\Client\Market;
use App\Enums\Client\Segment;
use App\Enums\Client\YearsInBusiness;
use App\Enums\Product\Color;
use App\Enums\Product\Material;
use App\Enums\Product\Type;
use App\Enums\Project\Status as ProjectStatus;
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
use App\Models\SystemTask;
use App\Support\Api\TeableClient;
use App\Support\SystemTasks\Contracts\SystemTaskRunner;
use App\Values\ScenarioRequestRequirements;
use App\Values\ScenarioRequestSettings;
use App\Values\ScenarioSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;
use RuntimeException;
use Vagebond\Bijlagen\Support\Factories\AttachmentFactory;

class ImportScenario implements SystemTaskRunner
{
    private array $clientMap = [];

    private array $productMap = [];

    private array $requestMap = [];

    public function handle(SystemTask $task)
    {
        $config = Config::collection('coffeeco.scenario_groups', [])
            ->firstWhere('base_id', '=', $task->payload['baseID']);

        if (empty($config)) {
            throw new RuntimeException('Invalid baseID / config.');
        }

        $scenario = $this->createScenario($config);

        $this->importScenarioData($scenario, $config);

        $scenario->update([
            'status' => Status::ACTIVE,
        ]);

        Scenario::query()
            ->where('group_id', '=', $scenario->group_id)
            ->where('id', '<', $scenario->id)
            ->whereIn('status', [
                Status::ACTIVE,
                Status::DRAFT,
                Status::PROCESSING,
            ])
            ->update(['status' => Status::ARCHIVED]);

        $task->update([
            'result' => [
                ...$task->result,
                'url' => route('admin.game-sessions.index', [$scenario]),
            ],
        ]);
    }

    private function createScenario(array $config)
    {
        return Scenario::create([
            'group_id' => hash('xxh3', $config['base_id']),
            'title' => $config['title'],
            'locale' => $config['locale'],
            'settings' => ScenarioSettings::fromArray([]),
            'status' => Status::PROCESSING,
        ]);
    }

    private function importScenarioData(Scenario $scenario, array $config)
    {
        $tables = $this->apiClient()->getTableIds($config['base_id'], [
            'clients',
            'products',
            'requests',
            'solutions',
            'tips',
            'codes',
        ]);

        $this->importClients($scenario, $tables['clients']);
        $this->importProducts($scenario, $tables['products']);
        $this->importRequests($scenario, $tables['requests']);
        $this->importSolutions($scenario, $tables['solutions']);
        $this->importTips($scenario, $tables['tips']);
        $this->importCodes($scenario, $tables['codes']);
    }

    private function importClients(Scenario $scenario, string $tableID)
    {
        $this->apiClient()->getAllRecords($tableID)
            ->each(fn($v) => $this->importClient($scenario, $v));
    }

    private function importClient(Scenario $scenario, array $record)
    {
        $segment = Str::kebab($record['fields']['segment']);
        $segment = Segment::from($segment);

        $market = Market::from(Str::slug($record['fields']['market']));
        $carBrand = CarBrand::from(strtolower($record['fields']['car']));
        $years = YearsInBusiness::fromInteger((int) $record['fields']['years in business']);

        $client = ScenarioClient::create([
            'scenario_id' => $scenario->id,
            'player_id' => (int) Arr::get($record, 'fields.sales person', 1),
            'title' => Arr::get($record, 'fields.name'),
            'segment' => $segment,
            'settings' => [
                'market' => $market->value,
                'carbrand' => $carBrand->value,
                'years' => $years->value,
            ],
            'sortorder' => 1,
        ]);

        $this->clientMap[$record['id']] = $client->id;

        if (! array_key_exists('logo', $record['fields'])) {
            return;
        }

        $logo = current($record['fields']['logo']);
        $parts = parse_url($logo['presignedUrl']);
        parse_str($parts['query'] ?? '', $query);

        $url = Uri::of(config('services.teable.api_endpoint'))
            ->withPath($parts['path'])
            ->withQuery($query);

        AttachmentFactory::fromUrl((string) $url)
            ->setSubject($client)
            ->setType(AttachmentType::ScenarioClientImage)
            ->store();
    }

    private function importProducts(Scenario $scenario, string $tableID)
    {
        $this->apiClient()->getAllRecords($tableID)
            ->each(fn($v) => $this->importProduct($scenario, $v));
    }

    private function importProduct(Scenario $scenario, array $record)
    {
        $fields = $record['fields'];

        $publicID = strtoupper($fields['public_id'] ?? '');
        if (empty($publicID) || strlen($publicID) !== 3) {
            throw new RuntimeException('Invalid public ID for product ' . $record['autoNumber']);
        }

        $type = Type::from($fields['type']);
        $material = Material::tryFrom($fields['material'] ?? '');
        $color = Color::tryFrom($fields['color'] ?? '');

        $product = ScenarioProduct::create([
            'scenario_id' => $scenario->id,
            'public_id' => $publicID,
            'type' => $type,
            'material' => $material,
            'color' => $color,
        ]);

        $this->productMap[$publicID] = $product->id;
    }

    private function importRequests(Scenario $scenario, string $tableID)
    {
        $this->apiClient()->getAllRecords($tableID)
            ->each(fn($v) => $this->importRequest($scenario, $v));
    }

    private function importRequest(Scenario $scenario, array $record)
    {
        $fields = $record['fields'];

        $clientID = $this->clientMap[$fields['client']['id']];

        $requirements = ScenarioRequestRequirements::fromArray([
            'hdma' => (bool) Arr::boolean($fields, 'hdma', false),
            'labconsulting' => (bool) Arr::boolean($fields, 'hdma', false),
            'tresholdNps' => (int) Arr::get($fields, 'npstreshold', 60),
            'tresholdMarketing' => (int) Arr::get($fields, 'marketingtreshold', 0),
            'customerdecision' => (int) Arr::get($fields, 'customerscoretreshold', 100),
        ]);

        $settings = ScenarioRequestSettings::fromArray([
            'value' => (int) Arr::get($fields, 'value', 100),
            'competitionprice' => (int) Arr::get($fields, 'competitionprice', 100),
            'competitionlevel' => CompetitionLevel::from(Arr::get($fields, 'competitionlevel', CompetitionLevel::MEDIUM->value)),
            'initialfailurechance' => (int) Arr::get($fields, 'initialfailurechance', 0),
            'initialstatus' => ProjectStatus::from(Arr::get($fields, 'initialstatus', ProjectStatus::ACTIVE->value)),
        ]);

        $request = ScenarioRequest::create([
            'scenario_id' => $scenario->id,
            'client_id' => $clientID,
            'description' => trim($fields['description']),
            'delay' => (int) $fields['delay'],
            'duration' => (int) $fields['duration'],
            'requirements' => $requirements,
            'settings' => $settings,
        ]);

        $this->requestMap[$record['id']] = $request->id;
    }

    private function importSolutions(Scenario $scenario, string $tableID)
    {
        $this->apiClient()->getAllRecords($tableID)
            ->each(fn($v) => $this->importSolution($scenario, $v));
    }

    private function importSolution(Scenario $scenario, array $record)
    {
        $fields = $record['fields'];

        $requestID = $this->requestMap[$fields['requestID']['id']];
        $productsIDs = collect(explode(',', $fields['productIDs']))
            ->map(fn($v) => $this->productMap[strtoupper($v)]);

        ScenarioRequestSolution::create([
            'scenario_id' => $scenario->id,
            'request_id' => $requestID,
            'product_ids' => $productsIDs,
            'score' => (int) Arr::get($fields, 'score', 100),
            'is_optimal' => (bool) Arr::boolean($fields, 'isOptimal', false),
        ]);
    }

    private function importTips(Scenario $scenario, string $tableID)
    {
        $this->apiClient()->getAllRecords($tableID)
            ->each(fn($v) => $this->importTip($scenario, $v));
    }

    private function importTip(Scenario $scenario, array $record)
    {
        $fields = $record['fields'];
        $content = trim($fields['content'] ?? '');

        if (empty($content)) {
            return;
        }

        ScenarioTip::create([
            'scenario_id' => $scenario->id,
            'content' => $content,
        ]);
    }

    private function importCodes(Scenario $scenario, string $tableID)
    {
        $this->apiClient()->getAllRecords($tableID)
            ->each(fn($v) => $this->importCode($scenario, $v));
    }

    private function importCode(Scenario $scenario, array $record)
    {
        $fields = $record['fields'];
        $code = trim($fields['code'] ?? '');

        if (empty($code)) {
            return;
        }

        $category = CampaignCodeCategory::tryFrom($fields['category']) ?? CampaignCodeCategory::EASY;

        ScenarioCampaignCode::create([
            'scenario_id' => $scenario->id,
            'code' => $code,
            'category' => $category,
        ]);
    }

    private function apiClient(): TeableClient
    {
        static $client;

        if (! $client) {
            $client = TeableClient::make();
        }

        return $client;
    }
}
