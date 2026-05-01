<?php

namespace EasyAI\LaravelAI\Drivers;

use EasyAI\LaravelAI\Contracts\AIResponseInterface;
use EasyAI\LaravelAI\Exceptions\ConnectionException;
use EasyAI\LaravelAI\Exceptions\ProviderException;
use EasyAI\LaravelAI\Response\AIResponse;
use Illuminate\Support\Facades\Http;

class OllamaDriver extends AbstractDriver
{
    public function getProviderName(): string
    {
        return 'ollama';
    }

    public function chat(array $messages): AIResponseInterface
    {
        $messages = $this->prependSystemPrompt($messages);
        $url      = rtrim($this->config['url'], '/') . '/api/chat';
        $isStream = $this->streamCallback !== null;

        $body = [
            'model'    => $this->currentModel,
            'messages' => $messages,
            'stream'   => $isStream,
            'options'  => [
                'temperature' => $this->getTemperature(),
            ],
        ];

        $this->log('Request', ['model' => $this->currentModel, 'messages_count' => count($messages)]);

        try {
            if ($isStream) {
                return $this->handleStream($url, $body);
            }

            $response = Http::timeout($this->getTimeout())
                ->post($url, $body);

            if (!$response->successful()) {
                throw new ProviderException(
                    "Ollama error: {$response->status()} - {$response->body()}",
                    'ollama',
                    ['status' => $response->status()],
                    $response->status()
                );
            }

            $data = $response->json();

            $result = new AIResponse(
                content:          $data['message']['content'] ?? '',
                promptTokens:     $data['prompt_eval_count'] ?? $this->estimateTokens(json_encode($messages)),
                completionTokens: $data['eval_count'] ?? $this->estimateTokens($data['message']['content'] ?? ''),
                model:            $data['model'] ?? $this->currentModel,
                provider:         'ollama',
                raw:              $data,
            );

            $this->log('Response', ['tokens' => $result->getTotalTokens()]);
            $this->resetOverrides();

            return $result;
        } catch (ProviderException $e) {
            $this->resetOverrides();
            throw $e;
        } catch (\Throwable $e) {
            $this->resetOverrides();
            throw new ConnectionException(
                "Ollama connection failed: {$e->getMessage()}",
                'ollama',
                ['url' => $url],
                0,
                $e
            );
        }
    }

    protected function handleStream(string $url, array $body): AIResponseInterface
    {
        $callback = $this->streamCallback;
        $fullContent = '';

        $response = Http::timeout($this->getTimeout())
            ->withOptions(['stream' => true])
            ->post($url, $body);

        $stream = $response->toPsrResponse()->getBody();
        $buffer = '';

        while (!$stream->eof()) {
            $buffer .= $stream->read(1024);
            $lines = explode("\n", $buffer);
            $buffer = array_pop($lines); // keep incomplete line

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                $json = json_decode($line, true);
                if (!$json) continue;

                $chunk = $json['message']['content'] ?? '';
                if ($chunk !== '') {
                    $fullContent .= $chunk;
                    $callback($chunk);
                }

                if (!empty($json['done'])) {
                    $result = new AIResponse(
                        content:          $fullContent,
                        promptTokens:     $json['prompt_eval_count'] ?? $this->estimateTokens(json_encode($body['messages'])),
                        completionTokens: $json['eval_count'] ?? $this->estimateTokens($fullContent),
                        model:            $json['model'] ?? $this->currentModel,
                        provider:         'ollama',
                        raw:              $json,
                    );
                    $this->resetOverrides();
                    return $result;
                }
            }
        }

        $this->resetOverrides();

        return new AIResponse(
            content:          $fullContent,
            promptTokens:     $this->estimateTokens(json_encode($body['messages'])),
            completionTokens: $this->estimateTokens($fullContent),
            model:            $this->currentModel,
            provider:         'ollama',
            raw:              [],
        );
    }

    public function health(): bool
    {
        try {
            $url = rtrim($this->config['url'], '/');
            $response = Http::timeout(5)->get($url);
            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    public function models(): array
    {
        try {
            $url = rtrim($this->config['url'], '/') . '/api/tags';
            $response = Http::timeout(10)->get($url);

            if (!$response->successful()) return [];

            return array_column($response->json('models', []), 'name');
        } catch (\Throwable) {
            return [];
        }
    }
}
