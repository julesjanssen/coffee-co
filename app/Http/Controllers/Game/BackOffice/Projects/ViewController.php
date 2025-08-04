<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\BackOffice\Projects;

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

        $productIDs = collect($request->input('products'))
            ->filter()
            ->map(fn($v) => strtoupper($v));

        $products = $session->scenario->products()
            ->whereIn('public_id', $productIDs)
            ->get();

        if ($products->count() !== $productIDs->count()) {
            throw ValidationException::withMessages([
                'products' => [__('One or more invalid product IDs provided.')],
            ]);
        }

        $solution = $project->request->solutions()
            ->whereJsonContains('product_ids', $products->pluck('id'))
            ->whereJsonLength('product_ids', '=', $products->count())
            ->first();

        return $solution;
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
