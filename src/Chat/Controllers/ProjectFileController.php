<?php

namespace EasyAI\LaravelAI\Chat\Controllers;

use EasyAI\LaravelAI\Chat\Models\Project;
use EasyAI\LaravelAI\Chat\Models\ProjectFile;
use EasyAI\LaravelAI\Facades\AI;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProjectFileController extends Controller
{
    public function index(Project $project)
    {
        return response()->json($project->files()->latest()->get());
    }

    public function store(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|file|mimes:txt,md,pdf|max:10240',
        ]);

        $uploadedFile = $request->file('file');
        $path         = $uploadedFile->store('project-files/' . $project->id, 'local');

        $pf = ProjectFile::create([
            'project_id'    => $project->id,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'stored_path'   => $path,
            'mime_type'     => $uploadedFile->getMimeType(),
            'status'        => 'pending',
        ]);

        try {
            $text = $this->extractText($pf);

            if (empty(trim($text))) {
                throw new \RuntimeException('File is empty or could not be read.');
            }

            $source = 'project_' . $project->id;

            // Ingest — return value may be int or void depending on package version
            AI::rag()->ingest($text, $source);

            // Verify chunks were actually stored
            $count = DB::table(config('ai.rag.table', 'ai_documents'))
                        ->where('source', $source)
                        ->count();

            if ($count === 0) {
                throw new \RuntimeException(
                    'Ingestion ran but no chunks were stored. ' .
                    'Check that nomic-embed-text is running: ollama pull nomic-embed-text'
                );
            }

            $pf->update(['status' => 'ingested']);

        } catch (\Throwable $e) {
            $pf->update(['status' => 'failed']);
            return response()->json([
                'file'  => $pf->fresh(),
                'error' => $e->getMessage(),
            ], 422);
        }

        return response()->json($pf->fresh(), 201);
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        Storage::disk('local')->delete($file->stored_path);
        $file->delete();
        return response()->json(['ok' => true]);
    }

    private function extractText(ProjectFile $file): string
    {
        $fullPath = Storage::disk('local')->path($file->stored_path);

        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Stored file not found at: {$fullPath}");
        }

        if (str_contains($file->mime_type, 'pdf')) {
            if (!class_exists(\Smalot\PdfParser\Parser::class)) {
                throw new \RuntimeException(
                    'PDF ingestion requires: composer require smalot/pdfparser'
                );
            }
            $parser = new \Smalot\PdfParser\Parser();
            return $parser->parseFile($fullPath)->getText();
        }

        $text = file_get_contents($fullPath);

        if ($text === false) {
            throw new \RuntimeException('Could not read file contents.');
        }

        return $text;
    }
}