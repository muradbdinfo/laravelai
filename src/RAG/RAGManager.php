<?php
namespace EasyAI\LaravelAI\RAG;
use EasyAI\LaravelAI\Facades\AI;
use Illuminate\Support\Facades\DB;

class RAGManager
{
    public function ingest(string $content, string $source = ''): int
    {
        $count = 0;
        foreach ($this->chunk($content) as $chunk) {
            $vector = $this->embed($chunk);
            DB::table(config('ai.rag.table'))->insert([
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
    $context  = collect($this->search($question))->pluck('content')->join("\n\n---\n\n");
    $provider = config('ai.rag.chat_provider') ?? config('ai.default');

    // Explicitly set chat model to avoid embed model bleeding over
    $chatModel = config("ai.providers.{$provider}.model");

    $ai = AI::provider($provider)->model($chatModel);

    if ($context) {
        $ai->systemPrompt(config('ai.rag.system_prompt') . "\n\nCONTEXT:\n" . $context);
    }

    return $ai->chat([['role' => 'user', 'content' => $question]])->content;
}

    public function search(string $query): array
    {
        $queryVector = $this->embed($query);
        return DB::table(config('ai.rag.table'))
            ->get(['content', 'source', 'embedding'])
            ->map(fn($row) => [
                'content' => $row->content,
                'source'  => $row->source,
                'score'   => $this->cosine($queryVector, json_decode($row->embedding, true)),
            ])
            ->sortByDesc('score')
            ->take(config('ai.rag.top_k', 3))
            ->values()
            ->toArray();
    }

    public function flush(): void
    {
        DB::table(config('ai.rag.table'))->truncate();
    }

    private function embed(string $text): array
    {
        return AI::provider(config('ai.rag.embed_provider', 'ollama'))
            ->model(config('ai.rag.embed_model', 'nomic-embed-text'))
            ->embed($text)[0];
    }

    private function chunk(string $text): array
    {
        $paragraphs = preg_split('/\n{2,}/', $text);
        $chunks  = [];
        $current = '';
        foreach ($paragraphs as $para) {
            if (strlen($current . $para) > config('ai.rag.chunk_size', 2000) && $current) {
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