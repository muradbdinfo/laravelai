<?php

namespace EasyAI\LaravelAI\Chat\Controllers;

use EasyAI\LaravelAI\Chat\Models\Project;
use EasyAI\LaravelAI\Facades\AI;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount('files')->latest()->get();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $project = Project::create($request->only('name', 'description'));

        return response()->json($project, 201);
    }

    public function destroy(Project $project)
    {
        // Remove all RAG vectors for this project
        try {
            AI::rag()->flush('project_' . $project->id);
        } catch (\Throwable $e) {
            // Non-fatal
        }

        $project->delete(); // cascades to project_files and nulls chat_sessions.project_id

        return response()->json(['ok' => true]);
    }
}
