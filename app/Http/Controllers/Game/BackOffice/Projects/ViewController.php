<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\BackOffice\Projects;

use App\Enums\GameSession\TransactionType;
use App\Enums\Project\Status;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request, Project $project)
    {
        if ($project->status->isNot(Status::PENDING)) {
            return redirect()->route('game.base');
        }

        $projectResource = [
            'title' => $project->title,
            'price' => $project->price,
            'client' => [
                'title' => $project->client->title,
            ],
        ];

        return Inertia::render('game/backoffice/projects/view', [
            'project' => $projectResource,
            'solutions' => $this->listSolutions($project),
            'links' => [
                'products.view' => route('game.products.view', ['XXX']),
            ],
        ]);
    }

    public function store(Request $request, Project $project)
    {
        if ($project->status->isNot(Status::PENDING)) {
            return redirect()->route('game.base');
        }

        $participant = $request->participant();
        $session = $participant->session;

        $request->validate([
            'price' => ['required', 'integer', 'min:1', 'max:1000'],
            'products' => ['required', 'array'],
            'products.*' => ['sometimes', 'nullable', 'string', 'min:3', 'max:5'],
        ]);

        $price = $request->integer('price');

        $productIDs = collect($request->input('products'))
            ->filter()
            ->map(fn($v) => strtoupper((string) $v));

        $products = $session->scenario->products()
            ->whereIn('public_id', $productIDs)
            ->get();

        if ($products->count() !== $productIDs->count()) {
            throw ValidationException::withMessages([
                'products' => [__('One or more invalid product IDs provided.')],
            ]);
        }

        $nps = $session->netPromotorScore();
        if ($nps < $project->request->requirements->tresholdNps) {
            return $this->updateProjectStatus($project, Status::LOST, __('The client is unsatisfied by your performance.'));
        }

        $solution = $project->request->solutions()
            ->whereJsonContains('product_ids', $products->pluck('id'))
            ->whereJsonLength('product_ids', '=', $products->count())
            ->first();

        if (empty($solution)) {
            return $this->updateProjectStatus($project, Status::LOST, __('Your offer did not match our request.'));
        }

        $project->solution_id = $solution->id;
        $project->price = $price;

        $competitionPrice = $project->request->settings->competitionprice;
        if ($competitionPrice > 0) {
            $priceDiff = ($competitionPrice - $price);
            $score = $solution->score + (($priceDiff / $competitionPrice) * 100);
        } else {
            $score = 100;
        }

        if ($score < $project->request->requirements->customerdecision) {
            $messages = [];
            $messages[] = __('You have lost the deal, :reason.', [
                'reason' => $solution->isOptimal()
                    ? __('but this was the optimal solution for this request')
                    : __('this was not the optimal solution for this request'),
            ]);

            $messages[] = __('The competition in this segment is :level.', [
                'level' => $project->request->settings->competitionlevel->description(),
            ]);

            $messages[] = __('The combination between your solution and your price was not optimal.');

            return $this->updateProjectStatus($project, Status::LOST, $messages);
        }

        $messages = [];
        $messages[] = __('Congratulations, you won the deal, :reason.', [
            'reason' => $solution->isOptimal()
                ? __('you offered the optimal solution for this request')
                : __('but you did not offer the optimal solution for this request'),
        ]);

        $messages[] = __('You need to deliver and install your solution to the client in :num months.', [
            'num' => $session->settings->roundsToDeliverProject,
        ]);

        $messages[] = __('Please find more information in your deals won overview.');
        $messages[] = __('You need to pick up the necessary materials at the materials warehouse and bring them to the pick up point for technical services to deliver them.');

        return $this->updateProjectStatus($project, Status::WON, $messages);
    }

    /**
     * @param string|string[] $reasons
     */
    private function updateProjectStatus(Project $project, Status $status, string|array $reasons)
    {
        $project->settings->wonLostReasons = is_array($reasons) ? $reasons : [$reasons];
        $project->status = $status;
        $project->quote_round_id = $project->session->current_round_id;
        $project->save();

        if ($status->is(Status::WON)) {
            $project->session->transactions()
                ->create([
                    'project_id' => $project->id,
                    'client_id' => $project->client_id,
                    'type' => TransactionType::PROJECT_WON,
                    'round_id' => $project->session->currentRound->roundID,
                    'value' => (int) round($project->price * .9),
                ]);
        }

        return redirect()->route('game.backoffice.projects.view', [$project]);
    }

    private function listSolutions(Project $project)
    {
        if (! App::isLocal()) {
            return [];
        }

        return $project->request->solutions
            ->sortByDesc('is_optimal')
            ->map(function ($solution) {
                $products = $solution->products();

                return [
                    'products' => $products->pluck('public_id'),
                ];
            });
    }
}
