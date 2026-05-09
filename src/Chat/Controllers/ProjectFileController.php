<?php

namespace EasyAI\LaravelAI\Chat\Controllers;

use EasyAI\LaravelAI\Chat\Models\Project;
use EasyAI\LaravelAI\Chat\Models\ProjectFile;
use EasyAI\LaravelAI\Facades\AI;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
            $this->ingest($pf);
            $pf->update(['status' => 'ingested']);
        } catch (\Throwable $e) {
            $pf->update(['status' => 'failed']);
            return response()->json([
                'file'  => $pf,
                'error' => 'File saved but ingestion failed: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json($pf->fresh(), 201);
    }

    public function destroy(Project $project, ProjectFile $file)
    {
        Storage::disk('local')->delete($file->stored_path);
        $file->delete();

        // Note: we don't delete individual chunks from ai_documents here
        // because source is shared per project. Full project delete handles that.
        // If you want per-file cleanup, store chunk IDs — future enhancement.

        return response()->json(['ok' => true]);
    }

    // ── Ingestion ────────────────────────────────────────────────────────

    private function ingest(ProjectFile $file): void
    {
        $text   = $this->extractText($file);
        $source = 'project_' . $file->project_id;
        AI::rag()->ingest($text, $source);
    }

    private function extractText(ProjectFile $file): string
    {
        $fullPath = Storage::disk('local')->path($file->stored_path);

        // PDF support — requires: composer require smalot/pdfparser
        if ($file->mime_type === 'application/pdf') {
            if (!class_exists(\Smalot\PdfParser\Parser::class)) {
                throw new \RuntimeException(
                    'PDF ingestion requires: composer require smalot/pdfparser'
                );
            }
            $parser = new \Smalot\PdfParser\Parser();
            return $parser->parseFile($fullPath)->getText();
        }

        // TXT / MD
        return file_get_contents($fullPath);
    }
}
