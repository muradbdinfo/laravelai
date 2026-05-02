<p align="center">
  <img src="https://raw.githubusercontent.com/muradbdinfo/laravelai/main/art/banner.svg" width="100%" alt="LaravelAI Banner">
</p>

<h1 align="center">LaravelAI</h1>

<p align="center">
  <strong>One interface, any AI.</strong><br>
  Unified AI chat for Laravel — Ollama, OpenAI (ChatGPT), Anthropic (Claude), DeepSeek
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
  <a href="#-providers">Providers</a> •
  <a href="#-features">Features</a> •
  <a href="#-real-world-examples">Examples</a> •
  <a href="#-api-reference">API Reference</a> •
  <a href="#-configuration">Configuration</a>
</p>

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
AI_OLLAMA_MODEL=llama3.1:8b
```

**That's it!** You're ready to use AI in your Laravel app.

### Requirements

| Requirement | Version |
|------------|---------|
| PHP | 8.2+ |
| Laravel | 10, 11, 12 |

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

## 🤖 Providers

LaravelAI supports 4 providers out of the box. Set up as many as you need.

### Ollama (Self-Hosted — Free)

Run AI models on your own server. No API key needed.

```env
AI_PROVIDER=ollama
AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=llama3.1:8b
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
    ->model('llama3.1:8b')        // Choose model
    ->temperature(0.9)             // Creativity (0 = focused, 2 = creative)
    ->maxTokens(500)               // Limit response length
    ->systemPrompt('You are a helpful Laravel expert.')
    ->chat([
        ['role' => 'user', 'content' => 'Explain middleware']
    ]);
```

### 2. System Prompts — Give AI a Persona

```php
// Make it a pirate
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

// "4 multiplied by 10 equals 40."
```

### 4. Streaming — Real-Time Output

Get tokens as they are generated, just like ChatGPT typing effect:

```php
AI::provider('ollama')->stream(
    [['role' => 'user', 'content' => 'Write a poem about coding']],
    function (string $chunk) {
        echo $chunk; // Each word appears in real-time
    }
);
```

**Use in Laravel HTTP response (SSE):**

```php
// In your Controller
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

Check if a provider is online before sending requests:

```php
if (AI::provider('ollama')->health()) {
    $response = AI::chat($messages);
} else {
    // Fallback to another provider
    $response = AI::provider('openai')->chat($messages);
}
```

### 6. List Available Models

```php
$models = AI::provider('ollama')->models();
// ['llama3.1:8b', 'qwen2:1.5b', 'phi3:mini']

$models = AI::provider('openai')->models();
// ['gpt-4o', 'gpt-4o-mini', 'gpt-3.5-turbo', ...]
```

### 7. Token Estimation

Estimate token count **before** sending to AI — no API call needed:

```php
// Estimate a string
$tokens = AI::estimateTokens('Hello world, how are you?');
// 8

// Estimate a full conversation
$tokens = AI::estimateTokens([
    ['role' => 'system', 'content' => 'You are helpful.'],
    ['role' => 'user', 'content' => 'Explain PHP in 100 words'],
]);
// ~25
```

### 8. Error Handling

LaravelAI throws specific exceptions you can catch:

```php
use EasyAI\LaravelAI\Exceptions\ConnectionException;
use EasyAI\LaravelAI\Exceptions\ProviderException;

try {
    $response = AI::provider('openai')->chat($messages);
} catch (ConnectionException $e) {
    // Provider is unreachable
    Log::error("AI connection failed: " . $e->getMessage());
} catch (ProviderException $e) {
    // API returned an error (401, 429, 500, etc.)
    Log::error("AI error [{$e->getProvider()}]: " . $e->getMessage());
}
```

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
    $providers = ['ollama', 'deepseek', 'openai'];

    foreach ($providers as $provider) {
        try {
            if (!AI::provider($provider)->health()) continue;

            return AI::provider($provider)
                ->chat([['role' => 'user', 'content' => $message]])
                ->content;
        } catch (\Throwable $e) {
            Log::warning("Provider {$provider} failed: {$e->getMessage()}");
            continue;
        }
    }

    throw new \RuntimeException('All AI providers are unavailable');
}
```

### Use in Artisan Command

```php
class AskAI extends Command
{
    protected $signature = 'ai:ask {question}';
    protected $description = 'Ask AI a question from terminal';

    public function handle()
    {
        $this->info('Thinking...');

        $answer = ai($this->argument('question'));

        $this->line($answer);
    }
}
```

```bash
php artisan ai:ask "What is dependency injection?"
```

### Use in Blade Template (via Controller)

```php
// Controller
public function about()
{
    $tagline = ai('Write a 10-word tagline for a Laravel AI package');
    return view('about', compact('tagline'));
}
```

```html
<!-- Blade -->
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

### Provider Methods (Chainable)

| Method | Description |
|--------|-------------|
| `->model('gpt-4o')` | Set the AI model |
| `->temperature(0.7)` | Set creativity (0-2) |
| `->maxTokens(500)` | Limit response length |
| `->systemPrompt('...')` | Set AI persona/instructions |
| `->timeout(30)` | Set request timeout in seconds |
| `->chat(array $messages)` | Send and get response |
| `->stream(array $messages, callable $fn)` | Stream response in real-time |
| `->health()` | Check if provider is online |
| `->models()` | List available models |

### Response Object

| Property / Method | Type | Description |
|-------------------|------|-------------|
| `$response->content` | `string` | The AI reply text |
| `$response->promptTokens` | `int` | Tokens in your message |
| `$response->completionTokens` | `int` | Tokens in AI reply |
| `$response->totalTokens` | `int` | Total tokens used |
| `$response->model` | `string` | Model that was used |
| `$response->provider` | `string` | Provider name |
| `$response->getRaw()` | `array` | Raw API response |
| `$response->toArray()` | `array` | All data as array |
| `(string) $response` | `string` | Cast to content string |

### Helper Function

```php
// Basic — uses default provider
ai('Your question here')

// With provider
ai('Your question', 'openai')

// With provider and model
ai('Your question', 'anthropic', 'claude-sonnet-4-20250514')
```

### Message Format

All providers use the same message format:

```php
$messages = [
    ['role' => 'system', 'content' => 'Your instructions'],    // Optional
    ['role' => 'user', 'content' => 'User message'],            // Required
    ['role' => 'assistant', 'content' => 'Previous AI reply'],  // For context
    ['role' => 'user', 'content' => 'Follow-up question'],
];
```

> **Note:** Anthropic (Claude) handles system messages differently — LaravelAI converts the format automatically. You don't need to worry about it.

---

## ⚙️ Configuration

Full `config/ai.php` reference:

```php
return [
    // Default provider when calling AI::chat() without specifying one
    'default' => env('AI_PROVIDER', 'ollama'),

    'providers' => [

        'ollama' => [
            'driver'  => 'ollama',
            'url'     => env('AI_OLLAMA_URL', 'http://127.0.0.1:11434'),
            'model'   => env('AI_OLLAMA_MODEL', 'llama3.1:8b'),
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

    // Optional: log all AI requests
    'logging' => [
        'enabled' => (bool) env('AI_LOG_ENABLED', false),
        'channel' => env('AI_LOG_CHANNEL', 'stack'),
    ],

    // Retry failed requests
    'retry' => [
        'times' => (int) env('AI_RETRY_TIMES', 2),
        'sleep' => (int) env('AI_RETRY_SLEEP', 1000), // milliseconds
    ],
];
```

### Complete `.env` Example

```env
# Default provider
AI_PROVIDER=ollama

# Ollama (self-hosted, free)
AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=llama3.1:8b
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

# Logging (optional)
AI_LOG_ENABLED=false
AI_LOG_CHANNEL=stack
```

---

## 🔌 Add Your Own Provider

LaravelAI uses Laravel's Manager pattern — adding a custom provider is simple:

```php
// 1. Create your driver class
class GroqDriver extends AbstractDriver
{
    public function getProviderName(): string { return 'groq'; }

    public function chat(array $messages): AIResponseInterface
    {
        // Your implementation here
    }

    public function health(): bool { /* ... */ }
    public function models(): array { /* ... */ }
}

// 2. Register in AppServiceProvider@boot
AI::extend('groq', function ($config) {
    return new GroqDriver($config);
});

// 3. Add config in config/ai.php providers
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
        'message' => ['content' => 'Mocked response'],
        'model' => 'llama3.1:8b',
        'prompt_eval_count' => 10,
        'eval_count' => 5,
        'done' => true,
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
| v2.0 | Embeddings (RAG support) | 🔜 Planned |
| v2.0 | Function/Tool calling | 🔜 Planned |
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

**Md Murad Hosen** — Full-Stack Developer from Chittagong, Bangladesh 🇧🇩

| Platform | Link |
|----------|------|
| 🌐 Website | [easyit.com.bd](https://www.easyit.com.bd) |
| 📺 YouTube | [EasyBD IT](https://youtube.com/@easybdit) |
| 📘 Facebook | [Murad Hosen](https://facebook.com/muradhosenofficial) |
| 📱 WhatsApp | [+8801827517700](https://wa.me/8801827517700) |
| 💻 GitHub | [muradbdinfo](https://github.com/muradbdinfo) |

---

## 📄 License

MIT License — see [LICENSE](LICENSE) for details.

Free to use in personal and commercial projects.

---

<p align="center">
  <sub>Made with ❤️ in Bangladesh 🇧🇩</sub><br>
  <sub>Built for the Laravel community worldwide</sub>
</p>