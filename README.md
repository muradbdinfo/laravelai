# Laravel AI

**One interface, any AI.** Unified AI chat for Laravel — Ollama, OpenAI (ChatGPT), Anthropic (Claude), DeepSeek.

[![Latest Version](https://img.shields.io/packagist/v/easyai/laravel-ai.svg)](https://packagist.org/packages/easyai/laravel-ai)
[![License](https://img.shields.io/packagist/l/easyai/laravel-ai.svg)](https://packagist.org/packages/easyai/laravel-ai)

## Installation

```bash
composer require easyai/laravel-ai
```

Publish the config:

```bash
php artisan vendor:publish --tag=ai-config
```

## Quick Start

```php
use EasyAI\LaravelAI\Facades\AI;

// Default provider (from config)
$response = AI::chat([
    ['role' => 'user', 'content' => 'Hello!']
]);

echo $response->content;          // "Hi! How can I help?"
echo $response->totalTokens;      // 42
echo $response->provider;         // "ollama"
```

## Switch Providers

```php
$response = AI::provider('openai')->chat($messages);
$response = AI::provider('anthropic')->chat($messages);
$response = AI::provider('deepseek')->chat($messages);
$response = AI::provider('ollama')->chat($messages);
```

## Chain Options

```php
$response = AI::provider('openai')
    ->model('gpt-4o')
    ->temperature(0.7)
    ->maxTokens(1000)
    ->systemPrompt('You are a helpful teacher.')
    ->chat($messages);
```

## Streaming

```php
AI::provider('ollama')
    ->model('llama3.1:8b')
    ->stream($messages, function (string $chunk) {
        echo $chunk;
    });
```

## Health Check

```php
AI::provider('ollama')->health();   // true or false
AI::provider('openai')->health();   // true or false
```

## List Models

```php
AI::provider('ollama')->models();
// ['llama3.1:8b', 'qwen2:1.5b', 'phi3:mini']
```

## Token Estimation

```php
AI::estimateTokens('Hello world');           // ~3
AI::estimateTokens($messagesArray);          // ~120
```

## Helper Function

```php
$answer = ai('What is Laravel?');
$answer = ai('What is Laravel?', 'openai');
$answer = ai('What is Laravel?', 'anthropic', 'claude-sonnet-4-20250514');
```

## Configuration

Set in `.env`:

```env
AI_PROVIDER=ollama

# Ollama
AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=llama3.1:8b

# OpenAI
AI_OPENAI_KEY=sk-...
AI_OPENAI_MODEL=gpt-4o-mini

# Anthropic
AI_ANTHROPIC_KEY=sk-ant-...
AI_ANTHROPIC_MODEL=claude-sonnet-4-20250514

# DeepSeek
AI_DEEPSEEK_KEY=sk-...
AI_DEEPSEEK_MODEL=deepseek-chat
```

## Custom Driver

Add your own AI provider:

```php
// AppServiceProvider@boot
AI::extend('groq', function ($config) {
    return new GroqDriver($config);
});

// Use it
AI::provider('groq')->chat($messages);
```

## Response Object

```php
$response->getContent();          // string
$response->getPromptTokens();     // int
$response->getCompletionTokens(); // int
$response->getTotalTokens();      // int
$response->getModel();            // string
$response->getProvider();         // string
$response->getRaw();              // array (raw API response)
$response->toArray();             // array
```

## Requirements

- PHP 8.1+
- Laravel 10, 11, or 12

## Testing

```bash
composer test
```

## Credits

- [Md Murad Hosen](https://www.easyit.com.bd)

## License

MIT License. See [LICENSE](LICENSE) for details.
