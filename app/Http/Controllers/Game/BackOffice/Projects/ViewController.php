<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\BackOffice\Projects;

use App\Enums\Project\Status;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        ];

        return Inertia::render('game/backoffice/projects/view', [
            'project' => $projectResource,
            'solutions' => $this->listSolutions($project),
        ]);
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
