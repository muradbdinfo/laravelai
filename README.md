<p align="center">
  <img src="https://raw.githubusercontent.com/muradbdinfo/laravelai/main/art/banner.svg" width="100%" alt="LaravelAI Banner">
</p>

<h1 align="center">LaravelAI</h1>

<p align="center">
  <strong>One interface, any AI.</strong><br>
  Unified AI chat for Laravel — Ollama, OpenAI (ChatGPT), Anthropic (Claude), DeepSeek
</p>

<p align="center">
  <sub>👨‍💻 Full Stack Laravel Vue Developer and DevOps Engineer</sub>
</p>

<p align="center">
  <a href="https://packagist.org/packages/muradbdinfo/laravelai"><img src="https://img.shields.io/packagist/v/muradbdinfo/laravelai.svg?style=flat-square&label=version" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/muradbdinfo/laravelai"><img src="https://img.shields.io/packagist/dt/muradbdinfo/laravelai.svg?style=flat-square&label=downloads" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/muradbdinfo/laravelai"><img src="https://img.shields.io/packagist/l/muradbdinfo/laravelai.svg?style=flat-square" alt="License"></a>
  <a href="https://packagist.org/packages/muradbdinfo/laravelai"><img src="https://img.shields.io/packagist/php-v/muradbdinfo/laravelai.svg?style=flat-square" alt="PHP Version"></a>
  <a href="https://github.com/muradbdinfo/laravelai/actions"><img src="https://img.shields.io/github/actions/workflow/status/muradbdinfo/laravelai/tests.yml?branch=main&style=flat-square&label=tests" alt="Tests"></a>
</p>

<p align="center">
  <a href="#-quick-start">Quick Start</a> •
  <a href="#-built-in-chat-ui">Chat UI</a> •
  <a href="#-providers">Providers</a> •
  <a href="#-features">Features</a> •
  <a href="#-rag-built-in">RAG</a> •
  <a href="#-real-world-examples">Examples</a> •
  <a href="#-api-reference">API Reference</a> •
  <a href="#-configuration">Configuration</a>
</p>

<p align="center">
  <a href="https://www.facebook.com/easybdit">📘 Facebook Page</a> •
  <a href="https://www.facebook.com/groups/eitbd">👥 Facebook Group</a> •
  <a href="https://chat.whatsapp.com/E3VV0K6lkrqEgXdngrt2Rk">💬 WhatsApp Group</a>
</p>

## 📺 Video Tutorials

<table>
  <tr>
    <td align="center" width="33%">
      <a href="https://youtu.be/m_HyTIBRAOE">
        <img src="https://img.youtube.com/vi/m_HyTIBRAOE/maxresdefault.jpg"
             alt="How to make self hosted AI Server" width="100%">
        <br><br>
        <strong>🖥️ How to Make Self-Hosted AI Server</strong>
      </a>
      <br>
      <sub>Step-by-step guide to setting up your own local AI server with Ollama</sub>
    </td>
    <td align="center" width="33%">
      <a href="https://youtu.be/pSwewtXqgP8">
        <img src="https://img.youtube.com/vi/pSwewtXqgP8/maxresdefault.jpg"
             alt="Laravel AI Package Implement" width="100%">
        <br><br>
        <strong>🚀 Laravel AI Package Implementation</strong>
      </a>
      <br>
      <sub>How to install and use LaravelAI package in your Laravel project</sub>
    </td>
    <td align="center" width="33%">
      <a href="https://youtu.be/pSwewtXqgP8">
        <img src="https://img.youtube.com/vi/pSwewtXqgP8/maxresdefault.jpg"
             alt="Built-in Chat UI" width="100%">
        <br><br>
        <strong>💬 Built-in Chat UI</strong>
      </a>
      <br>
      <sub>Zero-setup ChatGPT-like app included</sub>
    </td>
  </tr>
</table>

---

## Why LaravelAI?

Building AI features in Laravel? You'd normally need separate SDKs, different request formats, and custom error handling for each provider. **LaravelAI eliminates all that.**

```php
// Same code works with ANY provider — just change the name
$response = AI::provider('ollama')->chat($messages);    // Self-hosted
$response = AI::provider('openai')->chat($messages);    // ChatGPT
$response = AI::provider('anthropic')->chat($messages); // Claude
$response = AI::provider('deepseek')->chat($messages);  // DeepSeek
```

**Built on Laravel's driver pattern** — the same architecture behind Mail, Cache, and Queue. If you know Laravel, you already know how to use this.

---

## 📦 Installation

**Step 1:** Install via Composer

```bash
composer require muradbdinfo/laravelai
```

**Step 2:** Publish the config file

```bash
php artisan vendor:publish --tag=ai-config
```

**Step 3:** Add your provider settings to `.env`

```env
AI_PROVIDER=ollama
AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=qwen2:1.5b
```

**That's it!** You're ready to use AI in your Laravel app.

### Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.2+ |
| Laravel | 10, 11, 12, 13 |

---

## 🚀 Quick Start

### The Simplest Example — 3 Lines

```php
use EasyAI\LaravelAI\Facades\AI;

$response = AI::chat([
    ['role' => 'user', 'content' => 'What is Laravel?']
]);

echo $response->content;
// "Laravel is a PHP web application framework..."
```

### One-Liner Helper

```php
$answer = ai('What is Laravel?');
// Returns the AI response as a string — that's it!
```

### Test It Right Now

```bash
php artisan tinker

>>> AI::provider('ollama')->health()
=> true

>>> ai('Say hello in 3 words')
=> "Hello there, friend!"
```

---

## 💬 Built-in Chat UI

> **New in v1.3.0** — A full ChatGPT-like chat application included out of the box. Zero setup required.

Install the package and visit `/ai-chat` — that's it.

### What you get automatically

- ChatGPT-like sidebar with conversation history
- Create, switch, and delete chat sessions
- Streaming responses with real-time typing effect
- Full Markdown rendering with syntax-highlighted code blocks
- Copy button per message and per code block
- Live provider switcher — switch between Ollama, OpenAI, Claude, DeepSeek from the UI
- Database-persisted conversation history (survives page refresh and server restart)
- Auto-title: first message becomes the session title automatically
- Offline-safe assets — no CDN dependency

### Setup (3 commands)

```bash
# 1. Publish JS/CSS assets for offline use
php artisan vendor:publish --tag=ai-chat-assets

# 2. Run migrations (creates chat_sessions + chat_messages tables)
php artisan migrate

# 3. Visit your chat UI
# http://your-app.test/ai-chat
```

> Migrations and routes load **automatically** — no `AppServiceProvider` changes needed.

### Customize the UI

```bash
# Copy views to your app for full customization
php artisan vendor:publish --tag=ai-chat-views
# → resources/views/vendor/laravelai/chat.blade.php
```

Laravel checks your published view first, falls back to the package view — standard vendor override pattern.

### Routes registered automatically

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/ai-chat` | Chat UI |
| POST | `/ai-chat/api/sessions` | Create new session |
| DELETE | `/ai-chat/api/sessions/{id}` | Delete session |
| GET | `/ai-chat/api/stream` | SSE streaming endpoint |
| POST | `/ai-chat/api/provider` | Switch active provider |

### Switch providers from the UI

The sidebar includes a live dropdown to switch between providers without touching `.env`. The selected provider is stored in the session.

```env
# Configure all providers you want available
AI_PROVIDER=ollama          # default

AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=qwen2:1.5b

AI_OPENAI_KEY=sk-...
AI_OPENAI_MODEL=gpt-4o-mini

AI_ANTHROPIC_KEY=sk-ant-...
AI_ANTHROPIC_MODEL=claude-3-haiku-20240307

AI_DEEPSEEK_KEY=sk-...
AI_DEEPSEEK_MODEL=deepseek-chat
```

---

## 🤖 Providers

LaravelAI supports 4 providers out of the box. Set up as many as you need.

### Ollama (Self-Hosted — Free)

Run AI models on your own server. No API key needed.

```env
AI_PROVIDER=ollama
AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=qwen2:1.5b
```

```php
$response = AI::provider('ollama')->chat([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

### OpenAI (ChatGPT)

```env
AI_OPENAI_KEY=sk-your-api-key
AI_OPENAI_MODEL=gpt-4o-mini
```

```php
$response = AI::provider('openai')
    ->model('gpt-4o')
    ->chat([['role' => 'user', 'content' => 'Hello!']]);
```

### Anthropic (Claude)

```env
AI_ANTHROPIC_KEY=sk-ant-your-api-key
AI_ANTHROPIC_MODEL=claude-sonnet-4-20250514
```

```php
$response = AI::provider('anthropic')->chat([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

### DeepSeek

```env
AI_DEEPSEEK_KEY=sk-your-api-key
AI_DEEPSEEK_MODEL=deepseek-chat
```

```php
$response = AI::provider('deepseek')->chat([
    ['role' => 'user', 'content' => 'Hello!']
]);
```

---

## ✨ Features

### 1. Chain Options

Customize every aspect of your AI request with a fluent API:

```php
$response = AI::provider('ollama')
    ->model('qwen2:1.5b')        // Choose model
    ->temperature(0.9)             // Creativity (0 = focused, 2 = creative)
    ->maxTokens(500)               // Limit response length
    ->systemPrompt('You are a helpful Laravel expert.')
    ->chat([
        ['role' => 'user', 'content' => 'Explain middleware']
    ]);
```

### 2. System Prompts — Give AI a Persona

```php
$response = AI::systemPrompt('You are a pirate captain. Always talk like a pirate.')
    ->chat([['role' => 'user', 'content' => 'Tell me about PHP']]);

// "Arrr! PHP be a fine language for sailin' the web seas, matey!"
```

### 3. Multi-Turn Conversations

Send full conversation history for context-aware responses:

```php
$response = AI::chat([
    ['role' => 'system', 'content' => 'You are a math tutor.'],
    ['role' => 'user', 'content' => 'What is 2+2?'],
    ['role' => 'assistant', 'content' => '2+2 equals 4.'],
    ['role' => 'user', 'content' => 'Now multiply that by 10'],
]);
```

### 4. Streaming — Real-Time Output

Get tokens as they are generated, just like ChatGPT typing effect:

```php
AI::provider('ollama')->stream(
    [['role' => 'user', 'content' => 'Write a poem about coding']],
    function (string $chunk) {
        echo $chunk;
    }
);
```

**Use in Laravel HTTP response (SSE):**

```php
public function stream(Request $request)
{
    return response()->stream(function () use ($request) {
        AI::provider('ollama')->stream(
            [['role' => 'user', 'content' => $request->message]],
            function (string $chunk) {
                echo "data: " . json_encode(['text' => $chunk]) . "\n\n";
                ob_flush();
                flush();
            }
        );
        echo "data: [DONE]\n\n";
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
    ]);
}
```

### 5. Health Check

```php
if (AI::provider('ollama')->health()) {
    $response = AI::chat($messages);
} else {
    $response = AI::provider('openai')->chat($messages);
}
```

### 6. List Available Models

```php
$models = AI::provider('ollama')->models();
// ['qwen2:1.5b', 'phi3:mini']

$models = AI::provider('openai')->models();
// ['gpt-4o', 'gpt-4o-mini', 'gpt-3.5-turbo', ...]
```

### 7. Token Estimation

Estimate token count **before** sending to AI — no API call needed:

```php
$tokens = AI::estimateTokens('Hello world, how are you?');
// 8

$tokens = AI::estimateTokens([
    ['role' => 'system', 'content' => 'You are helpful.'],
    ['role' => 'user', 'content' => 'Explain PHP in 100 words'],
]);
// ~25
```

### 8. Ollama Advanced Features

```php
// JSON / Structured output
$response = AI::provider('ollama')
    ->format('json')
    ->chat([['role' => 'user', 'content' => 'List 3 fruits as JSON']]);

// Embeddings
$vector = AI::provider('ollama')->embed('Hello world');

// Keep model in memory
AI::provider('ollama')->keepAlive('10m')->chat($messages);

// Custom Ollama options
AI::provider('ollama')
    ->options(['temperature' => 0.5, 'top_p' => 0.9])
    ->chat($messages);

// Model management
AI::provider('ollama')->pullModel('llama3.1:8b');
AI::provider('ollama')->showModel('qwen2:1.5b');
AI::provider('ollama')->runningModels();
AI::provider('ollama')->copyModel('qwen2:1.5b', 'my-model');
AI::provider('ollama')->deleteModel('old-model');
```

### 9. Error Handling

```php
use EasyAI\LaravelAI\Exceptions\ConnectionException;
use EasyAI\LaravelAI\Exceptions\ProviderException;

try {
    $response = AI::provider('openai')->chat($messages);
} catch (ConnectionException $e) {
    Log::error("AI connection failed: " . $e->getMessage());
} catch (ProviderException $e) {
    Log::error("AI error [{$e->getProvider()}]: " . $e->getMessage());
}
```

### 10. Custom Drivers

```php
// 1. Create your driver class
class GroqDriver extends AbstractDriver
{
    public function getProviderName(): string { return 'groq'; }
    public function chat(array $messages): AIResponseInterface { /* ... */ }
    public function health(): bool { /* ... */ }
    public function models(): array { /* ... */ }
}

// 2. Register in AppServiceProvider@boot
AI::extend('groq', function ($config) {
    return new GroqDriver($config);
});

// 3. Add config in config/ai.php
'groq' => [
    'driver'  => 'groq',
    'api_key' => env('AI_GROQ_KEY'),
    'url'     => 'https://api.groq.com/openai/v1',
    'model'   => 'llama-3.1-70b-versatile',
    'timeout' => 30,
],

// 4. Use it!
AI::provider('groq')->chat($messages);
```

---

## 🧠 RAG (Built-in)

LaravelAI includes a built-in RAG (Retrieval-Augmented Generation) system — store documents as embeddings, search by similarity, and let AI answer questions using your own data. No external vector database required.

### Setup

```bash
# 1. Pull an embedding model
ollama pull nomic-embed-text

# 2. Migrations run automatically — just run:
php artisan migrate
```

Add to `.env`:

```env
AI_RAG_PROVIDER=ollama
AI_RAG_EMBED_MODEL=nomic-embed-text
AI_RAG_CHAT_PROVIDER=ollama
```

### Ingest Documents

```php
// Ingest a string
AI::rag()->ingest('Laravel is a PHP framework using MVC pattern.', 'docs');

// Ingest via Artisan
// php artisan ai:rag:ingest storage/docs/manual.txt --source=manual
// php artisan ai:rag:ingest storage/docs/ --flush
```

### Ask Questions

```php
$answer = AI::rag()->ask('What is Laravel?');
// "Laravel is a PHP framework using MVC pattern..."
```

### Search (without AI)

```php
$results = AI::rag()->search('MVC pattern');
// [['content' => '...', 'source' => 'docs', 'score' => 0.91]]
```

### Flush All Documents

```php
AI::rag()->flush();
```

### RAG in a Controller

```php
class DocsController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate(['question' => 'required|string']);

        return response()->json([
            'answer' => AI::rag()->ask($request->question),
        ]);
    }
}
```

### RAG Configuration

```php
'rag' => [
    'embed_provider' => env('AI_RAG_PROVIDER', 'ollama'),
    'embed_model'    => env('AI_RAG_EMBED_MODEL', 'nomic-embed-text'),
    'chat_provider'  => env('AI_RAG_CHAT_PROVIDER', null),
    'chunk_size'     => (int) env('AI_RAG_CHUNK_SIZE', 2000),
    'top_k'          => (int) env('AI_RAG_TOP_K', 3),
    'table'          => env('AI_RAG_TABLE', 'ai_documents'),
    'system_prompt'  => env('AI_RAG_SYSTEM_PROMPT', 'Answer using ONLY the context below. If unsure, say so.'),
],
```

| `.env` Key | Default | Description |
|------------|---------|-------------|
| `AI_RAG_PROVIDER` | `ollama` | Provider for generating embeddings |
| `AI_RAG_EMBED_MODEL` | `nomic-embed-text` | Embedding model |
| `AI_RAG_CHAT_PROVIDER` | `null` (uses default) | Provider for chat in `ask()` |
| `AI_RAG_CHUNK_SIZE` | `2000` | Max characters per chunk |
| `AI_RAG_TOP_K` | `3` | Chunks to retrieve per query |
| `AI_RAG_TABLE` | `ai_documents` | Database table name |

---

## 💡 Real-World Examples

### AI Chatbot Controller

```php
class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $response = AI::chat([
            ['role' => 'user', 'content' => $request->message],
        ]);

        return response()->json([
            'reply'  => $response->content,
            'tokens' => $response->totalTokens,
        ]);
    }
}
```

### AI Translator

```php
public function translate(Request $request)
{
    $response = AI::systemPrompt(
        "Translate to {$request->language}. Return ONLY the translation."
    )->chat([
        ['role' => 'user', 'content' => $request->text],
    ]);

    return response()->json(['translated' => $response->content]);
}
```

### AI Code Reviewer

```php
public function review(Request $request)
{
    $response = AI::systemPrompt(
        'You are a senior code reviewer. Find bugs, security issues, and suggest improvements.'
    )->chat([
        ['role' => 'user', 'content' => "Review:\n```\n{$request->code}\n```"],
    ]);

    return response()->json(['review' => $response->content]);
}
```

### AI Email Writer

```php
public function writeEmail(Request $request)
{
    $response = AI::systemPrompt(
        "Write a {$request->tone} email. Include subject and body."
    )->chat([
        ['role' => 'user', 'content' => $request->context],
    ]);

    return response()->json(['email' => $response->content]);
}
```

### AI Content Summarizer

```php
public function summarize(Request $request)
{
    $response = AI::systemPrompt(
        'Summarize the following text in 3 bullet points. Be concise.'
    )->chat([
        ['role' => 'user', 'content' => $request->text],
    ]);

    return response()->json(['summary' => $response->content]);
}
```

### Provider Fallback Pattern

```php
public function chatWithFallback(string $message): string
{
    foreach (['ollama', 'deepseek', 'openai'] as $provider) {
        try {
            if (!AI::provider($provider)->health()) continue;

            return AI::provider($provider)
                ->chat([['role' => 'user', 'content' => $message]])
                ->content;
        } catch (\Throwable $e) {
            Log::warning("Provider {$provider} failed: {$e->getMessage()}");
        }
    }

    throw new \RuntimeException('All AI providers are unavailable');
}
```

### Use in Artisan Command

```php
class AskAI extends Command
{
    protected $signature   = 'ai:ask {question}';
    protected $description = 'Ask AI a question from terminal';

    public function handle()
    {
        $this->info('Thinking...');
        $this->line(ai($this->argument('question')));
    }
}
```

```bash
php artisan ai:ask "What is dependency injection?"
```

### Use in Blade Template (via Controller)

```php
public function about()
{
    $tagline = ai('Write a 10-word tagline for a Laravel AI package');
    return view('about', compact('tagline'));
}
```

```html
<p class="tagline">{{ $tagline }}</p>
```

---

## 📖 API Reference

### Facade Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `AI::chat(array $messages)` | `AIResponse` | Send chat with default provider |
| `AI::provider(string $name)` | `AIProvider` | Switch to a specific provider |
| `AI::estimateTokens(string\|array $input)` | `int` | Estimate token count offline |
| `AI::rag()` | `RAGManager` | Access the built-in RAG system |

### Provider Methods (Chainable)

| Method | Description |
|--------|-------------|
| `->model($name)` | Set the AI model |
| `->temperature($float)` | Set creativity (0–2) |
| `->maxTokens($int)` | Max tokens in response |
| `->systemPrompt($text)` | Set AI persona/instructions |
| `->timeout($seconds)` | Request timeout |
| `->chat(array $messages)` | Send and get full response |
| `->stream(array $messages, callable $fn)` | Stream response in real-time |
| `->health()` | Check if provider is online |
| `->models()` | List available models |

### Ollama-Only Methods

| Method | Description |
|--------|-------------|
| `->format('json')` | Force JSON output |
| `->embed($text)` | Generate vector embedding |
| `->keepAlive($duration)` | Keep model loaded in memory |
| `->options($array)` | Set raw Ollama options |
| `->pullModel($name)` | Download a model |
| `->showModel($name)` | Get model details |
| `->deleteModel($name)` | Remove a model |
| `->copyModel($src, $dst)` | Copy a model |
| `->runningModels()` | List loaded models |

### AIResponse Object

| Property | Type | Description |
|----------|------|-------------|
| `$response->content` | `string` | The AI reply text |
| `$response->model` | `string` | Model used |
| `$response->promptTokens` | `int` | Tokens in your message |
| `$response->replyTokens` | `int` | Tokens in AI reply |
| `$response->totalTokens` | `int` | Total tokens used |
| `$response->provider` | `string` | Provider name |
| `$response->getRaw()` | `array` | Raw API response |
| `$response->toArray()` | `array` | All data as array |
| `(string) $response` | `string` | Cast to content string |

### Helper Function

```php
ai('Your question here')                                        // default provider
ai('Your question', 'openai')                                   // specific provider
ai('Your question', 'anthropic', 'claude-3-haiku-20240307')    // provider + model
```

### Message Format

```php
$messages = [
    ['role' => 'system',    'content' => 'Your instructions'],   // optional
    ['role' => 'user',      'content' => 'User message'],
    ['role' => 'assistant', 'content' => 'Previous AI reply'],   // for context
    ['role' => 'user',      'content' => 'Follow-up question'],
];
```

> **Note:** Anthropic (Claude) handles system messages differently — LaravelAI converts the format automatically.

---

## ⚙️ Configuration

After publishing (`php artisan vendor:publish --tag=ai-config`), edit `config/ai.php`:

```php
return [
    'default' => env('AI_PROVIDER', 'ollama'),

    'providers' => [

        'ollama' => [
            'driver'  => 'ollama',
            'url'     => env('AI_OLLAMA_URL', 'http://127.0.0.1:11434'),
            'model'   => env('AI_OLLAMA_MODEL', 'qwen2:1.5b'),
            'timeout' => (int) env('AI_OLLAMA_TIMEOUT', 120),
            'options' => ['temperature' => 0.7],
        ],

        'openai' => [
            'driver'  => 'openai',
            'api_key' => env('AI_OPENAI_KEY'),
            'url'     => env('AI_OPENAI_URL', 'https://api.openai.com/v1'),
            'model'   => env('AI_OPENAI_MODEL', 'gpt-4o-mini'),
            'timeout' => (int) env('AI_OPENAI_TIMEOUT', 60),
            'options' => ['temperature' => 0.7, 'max_tokens' => 2000],
        ],

        'anthropic' => [
            'driver'  => 'anthropic',
            'api_key' => env('AI_ANTHROPIC_KEY'),
            'url'     => env('AI_ANTHROPIC_URL', 'https://api.anthropic.com/v1'),
            'model'   => env('AI_ANTHROPIC_MODEL', 'claude-sonnet-4-20250514'),
            'version' => env('AI_ANTHROPIC_VERSION', '2023-06-01'),
            'timeout' => (int) env('AI_ANTHROPIC_TIMEOUT', 60),
            'options' => ['max_tokens' => 2000],
        ],

        'deepseek' => [
            'driver'  => 'deepseek',
            'api_key' => env('AI_DEEPSEEK_KEY'),
            'url'     => env('AI_DEEPSEEK_URL', 'https://api.deepseek.com/v1'),
            'model'   => env('AI_DEEPSEEK_MODEL', 'deepseek-chat'),
            'timeout' => (int) env('AI_DEEPSEEK_TIMEOUT', 60),
            'options' => ['temperature' => 0.7, 'max_tokens' => 2000],
        ],
    ],

    'logging' => [
        'enabled' => (bool) env('AI_LOG_ENABLED', false),
        'channel' => env('AI_LOG_CHANNEL', 'stack'),
    ],

    'retry' => [
        'times' => (int) env('AI_RETRY_TIMES', 2),
        'sleep' => (int) env('AI_RETRY_SLEEP', 1000),
    ],

    'rag' => [
        'embed_provider' => env('AI_RAG_PROVIDER', 'ollama'),
        'embed_model'    => env('AI_RAG_EMBED_MODEL', 'nomic-embed-text'),
        'chat_provider'  => env('AI_RAG_CHAT_PROVIDER', null),
        'chunk_size'     => (int) env('AI_RAG_CHUNK_SIZE', 2000),
        'top_k'          => (int) env('AI_RAG_TOP_K', 3),
        'table'          => env('AI_RAG_TABLE', 'ai_documents'),
        'system_prompt'  => env('AI_RAG_SYSTEM_PROMPT', 'Answer using ONLY the context below. If unsure, say so.'),
    ],
];
```

### Complete `.env` Example

```env
# Default provider
AI_PROVIDER=ollama

# Ollama (self-hosted, free)
AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=qwen2:1.5b
AI_OLLAMA_TIMEOUT=120

# OpenAI (ChatGPT)
AI_OPENAI_KEY=sk-proj-xxxx
AI_OPENAI_MODEL=gpt-4o-mini
AI_OPENAI_TIMEOUT=60

# Anthropic (Claude)
AI_ANTHROPIC_KEY=sk-ant-xxxx
AI_ANTHROPIC_MODEL=claude-sonnet-4-20250514
AI_ANTHROPIC_TIMEOUT=60

# DeepSeek
AI_DEEPSEEK_KEY=sk-xxxx
AI_DEEPSEEK_MODEL=deepseek-chat
AI_DEEPSEEK_TIMEOUT=60

# RAG (built-in)
AI_RAG_PROVIDER=ollama
AI_RAG_EMBED_MODEL=nomic-embed-text
AI_RAG_CHAT_PROVIDER=ollama
AI_RAG_CHUNK_SIZE=2000
AI_RAG_TOP_K=3
AI_RAG_TABLE=ai_documents

# Logging (optional)
AI_LOG_ENABLED=false
AI_LOG_CHANNEL=stack
```

---

## 🧪 Testing

```bash
# Run all tests
vendor/bin/phpunit

# Run specific test
vendor/bin/phpunit --filter=test_ollama_chat
```

The package uses `Http::fake()` for testing — no real API calls needed:

```php
use EasyAI\LaravelAI\Facades\AI;
use Illuminate\Support\Facades\Http;

Http::fake([
    '127.0.0.1:11434/api/chat' => Http::response([
        'message'           => ['content' => 'Mocked response'],
        'model'             => 'qwen2:1.5b',
        'prompt_eval_count' => 10,
        'eval_count'        => 5,
        'done'              => true,
    ]),
]);

$response = AI::chat([['role' => 'user', 'content' => 'Hi']]);
$this->assertEquals('Mocked response', $response->content);
```

---

## 🗺️ Roadmap

| Version | Feature | Status |
|---------|---------|--------|
| v1.0 | Ollama, OpenAI, Anthropic, DeepSeek | ✅ Released |
| v1.1 | Laravel 12 & 13 support | ✅ Released |
| v1.2 | Built-in RAG system (embed, ingest, search, ask) | ✅ Released |
| v1.2 | Ollama: embeddings, JSON mode, model management | ✅ Released |
| v1.3 | Built-in Chat UI — zero-setup ChatGPT-like app | ✅ Released |
| v2.0 | Function / Tool calling | 🔜 Planned |
| v2.0 | Vision / Image input | 🔜 Planned |
| v2.1 | Groq driver | 🔜 Planned |
| v2.1 | Google Gemini driver | 🔜 Planned |
| v2.2 | Response caching | 🔜 Planned |
| v3.0 | Image generation | 🔜 Planned |

---

## ❤️ Support & Donations

If this package saves you time, consider supporting its development:

<p align="center">
  <a href="https://buymeacoffee.com/muradbdinfo">
    <img src="https://img.shields.io/badge/Buy%20Me%20A%20Coffee-FFDD00?style=for-the-badge&logo=buy-me-a-coffee&logoColor=black" alt="Buy Me A Coffee">
  </a>
  &nbsp;&nbsp;
  <a href="https://github.com/sponsors/muradbdinfo">
    <img src="https://img.shields.io/badge/GitHub%20Sponsors-EA4AAA?style=for-the-badge&logo=github-sponsors&logoColor=white" alt="GitHub Sponsors">
  </a>
</p>

**Other ways to support:**
- ⭐ Star this repo on GitHub
- 🐛 Report bugs or suggest features via [Issues](https://github.com/muradbdinfo/laravelai/issues)
- 🔀 Submit a pull request
- 📢 Share with your developer friends

---

## 👤 Credits

**Md Murad Hosen** — Full-Stack Laravel Vue Developer and DevOps Engineer from Chittagong, Bangladesh 🇧🇩

### Personal Links

| Platform | Link |
|----------|------|
| 🌐 Website | [easyit.com.bd](https://www.easyit.com.bd) |
| 📺 YouTube | [EasyBD IT](https://youtube.com/@easybdit) |
| 📘 Facebook | [Murad Hosen](https://facebook.com/muradhosenofficial) |
| 📱 WhatsApp | [+8801827517700](https://wa.me/8801827517700) |
| 💻 GitHub | [muradbdinfo](https://github.com/muradbdinfo) |

### Community Links

| Platform | Link |
|----------|------|
| 📘 Facebook Page | [EasyBD IT](https://www.facebook.com/easybdit) |
| 👥 Facebook Group | [EITBD](https://www.facebook.com/groups/eitbd) |
| 💬 WhatsApp Group | [Join Chat](https://chat.whatsapp.com/E3VV0K6lkrqEgXdngrt2Rk) |

---

## 📄 License

MIT License — see [LICENSE](LICENSE) for details.

Free to use in personal and commercial projects.

---

<p align="center">
  <sub>Made with ❤️ in Bangladesh 🇧🇩</sub><br>
  <sub>Built for the Laravel community worldwide</sub>
</p>