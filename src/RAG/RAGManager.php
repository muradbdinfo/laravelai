<?php
namespace EasyAI\LaravelAI\RAG;

use EasyAI\LaravelAI\Facades\AI;
use Illuminate\Support\Facades\DB;

class RAGManager
{
    private string  $embedProvider;
    private string  $embedModel;
    private ?string $chatProvider;
    private int     $chunkSize;
    private int     $topK;
    private string  $table;
    private string  $systemPrompt;

    public function __construct()
    {
        $this->embedProvider = config('ai.rag.embed_provider');
        $this->embedModel    = config('ai.rag.embed_model');
        $this->chatProvider  = config('ai.rag.chat_provider');
        $this->chunkSize     = config('ai.rag.chunk_size');
        $this->topK          = config('ai.rag.top_k');
        $this->table         = config('ai.rag.table');
        $this->systemPrompt  = config('ai.rag.system_prompt');
    }

    public function ingest(string $content, string $source = ''): int
    {
        $count = 0;
        foreach ($this->chunk($content) as $chunk) {
            $vector = $this->embed($chunk);
            DB::table($this->table)->insert([
                'content'    => $chunk,
                'source'     => $source,
                'embedding'  => json_encode($vector),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $count++;
        }
        return $count;
    }

    public function ask(string $question): string
    {
        $context = collect($this->search($question))->pluck('content')->join("\n\n---\n\n");
        $provider = $this->chatProvider ?? config('ai.default');

        $ai = AI::provider($provider);

        if ($context) {
            $ai->systemPrompt($this->systemPrompt . "\n\nCONTEXT:\n" . $context);
        }

        return $ai->chat([['role' => 'user', 'content' => $question]])->content;
    }

    public function search(string $query): array
    {
        $queryVector = $this->embed($query);

        return DB::table($this->table)
            ->get(['content', 'source', 'embedding'])
            ->map(fn($row) => [
                'content' => $row->content,
                'source'  => $row->source,
                'score'   => $this->cosine($queryVector, json_decode($row->embedding, true)),
            ])
            ->sortByDesc('score')
            ->take($this->topK)
            ->values()
            ->toArray();
    }

    public function flush(): void
    {
        DB::table($this->table)->truncate();
    }

    private function embed(string $text): array
    {
        return AI::provider($this->embedProvider)
            ->model($this->embedModel)
            ->embed($text)[0];
    }

    private function chunk(string $text): array
    {
        $paragraphs = preg_split('/\n{2,}/', $text);
        $chunks = [];
        $current = '';

        foreach ($paragraphs as $para) {
            if (strlen($current . $para) > $this->chunkSize && $current) {
                $chunks[] = trim($current);
                $current  = $para;
            } else {
                $current .= "\n\n" . $para;
            }
        }

        if (trim($current)) $chunks[] = trim($current);
        return $chunks ?: [$text];
    }

    private function cosine(array $a, array $b): float
    {
        $dot  = array_sum(array_map(fn($x, $y) => $x * $y, $a, $b));
        $magA = sqrt(array_sum(array_map(fn($x) => $x * $x, $a)));
        $magB = sqrt(array_sum(array_map(fn($x) => $x * $x, $b)));
        return ($magA && $magB) ? $dot / ($magA * $magB) : 0.0;
    }
}