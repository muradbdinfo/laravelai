[![LaravelAI Banner](https://rawcdn.githack.com/muradbdinfo/laravelai/main/art/banner.svg)](https://raw.githubusercontent.com/muradbdinfo/laravelai/main/art/banner.svg)

# LaravelAI

**One interface, any AI.**
Unified AI chat for Laravel — Ollama, OpenAI (ChatGPT), Anthropic (Claude), DeepSeek

👨‍💻 Full Stack Laravel Vue Developer and DevOps Engineer

[![Latest Version](https://img.shields.io/packagist/v/muradbdinfo/laravelai.svg?style=flat-square&label=version)](https://packagist.org/packages/muradbdinfo/laravelai)
[![Total Downloads](https://img.shields.io/packagist/dt/muradbdinfo/laravelai.svg?style=flat-square&label=downloads)](https://packagist.org/packages/muradbdinfo/laravelai)
[![License](https://img.shields.io/packagist/l/muradbdinfo/laravelai.svg?style=flat-square)](https://packagist.org/packages/muradbdinfo/laravelai)
[![PHP Version](https://img.shields.io/packagist/php-v/muradbdinfo/laravelai.svg?style=flat-square)](https://packagist.org/packages/muradbdinfo/laravelai)
[![Tests](https://img.shields.io/github/actions/workflow/status/muradbdinfo/laravelai/tests.yml?branch=main&style=flat-square&label=tests)](https://github.com/muradbdinfo/laravelai/actions)

[Quick Start](#-quick-start) • [Chat UI](#-built-in-chat-ui) • [Projects](#-projects--knowledge-bases) • [RAG](#-rag-built-in) • [Providers](#-providers) • [API Reference](#-api-reference) • [Configuration](#️-configuration)

[📘 Facebook Page](https://www.facebook.com/easybdit) • [👥 Facebook Group](https://www.facebook.com/groups/eitbd) • [💬 WhatsApp Group](https://chat.whatsapp.com/E3VV0K6lkrqEgXdngrt2Rk)

---

## 📺 Video Tutorials

- [🖥️ How to Make Self-Hosted AI Server](https://youtu.be/m_HyTIBRAOE)
- [🚀 Laravel AI Package Implementation](https://youtu.be/pSwewtXqgP8)
- [💬 Built-in Chat UI](https://youtu.be/pSwewtXqgP8)

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

**Built on Laravel's driver pattern** — the same architecture behind Mail, Cache, and Queue.

---

## 📦 Installation

```bash
composer require muradbdinfo/laravelai
php artisan vendor:publish --tag=ai-config
```

Add to `.env`:

```env
AI_PROVIDER=ollama
AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=qwen2:1.5b
```

### Requirements

| Requirement | Version |
|-------------|---------|
| PHP         | 8.2+    |
| Laravel     | 10, 11, 12, 13 |

---

## 🚀 Quick Start

```php
use EasyAI\LaravelAI\Facades\AI;

$response = AI::chat([
    ['role' => 'user', 'content' => 'What is Laravel?']
]);

echo $response->content;
```

### One-Liner Helper

```php
$answer = ai('What is Laravel?');
```

---

## 💬 Built-in Chat UI

> **New in v1.3.0** — A full ChatGPT-like chat application included out of the box.

Visit `/ai-chat` after install — that's it.

### Setup (3 commands)

```bash
php artisan vendor:publish --tag=ai-chat-assets
php artisan migrate
# Visit: http://your-app.test/ai-chat
```

### What you get

- ChatGPT-like sidebar with conversation history
- Create, switch, and delete chat sessions
- Streaming responses with real-time typing effect
- Full Markdown rendering with syntax-highlighted code blocks
- Copy button per message and per code block
- Live provider switcher (Ollama, OpenAI, Claude, DeepSeek)
- Database-persisted conversation history
- Auto-title: first message becomes the session title
- Offline-safe assets — no CDN dependency
- **Projects with RAG knowledge bases** (v1.4.0)

### Customize the UI

```bash
php artisan vendor:publish --tag=ai-chat-views
# → resources/views/vendor/laravelai/chat.blade.php
```

### Routes registered automatically

| Method | URL | Description |
|--------|-----|-------------|
| GET    | `/ai-chat` | Chat UI |
| POST   | `/ai-chat/api/sessions` | Create session |
| DELETE | `/ai-chat/api/sessions/{id}` | Delete session |
| GET    | `/ai-chat/api/stream` | SSE streaming |
| POST   | `/ai-chat/api/provider` | Switch provider |
| GET    | `/ai-chat/api/projects` | List projects |
| POST   | `/ai-chat/api/projects` | Create project |
| DELETE | `/ai-chat/api/projects/{id}` | Delete project |
| GET    | `/ai-chat/api/projects/{id}/files` | List project files |
| POST   | `/ai-chat/api/projects/{id}/files` | Upload & ingest file |
| DELETE | `/ai-chat/api/projects/{id}/files/{fid}` | Delete file |

---

## 🗂️ Projects & Knowledge Bases

> **New in v1.4.0** — Self-hosted Claude-like Projects. Create knowledge bases, upload documents, and get RAG-powered answers scoped to each project.

### How it works

1. Create a project from the sidebar
2. Upload `.txt`, `.md`, or `.pdf` files — they are chunked and ingested into RAG automatically
3. Start a chat inside the project — every message retrieves relevant chunks from **only that project's documents** as context
4. Normal chats (outside projects) are unaffected

### RAG scoping API

```php
// Search only project 5's documents
$results = AI::rag()->source('project_5')->search('your query');

// Ask with project-scoped context
$answer = AI::rag()->source('project_5')->ask('your question');

// Delete all vectors for a project
AI::rag()->flush('project_5');
```

### PDF support (optional)

```bash
composer require smalot/pdfparser
```

Without this, only `.txt` and `.md` files are supported.

### Project UI features

- 📁 **Projects section** in the sidebar with file count badge
- 📎 **File manager modal** — upload, view status (pending/ingested/failed), delete files
- 🧠 **RAG badge** in chat header when inside a project session
- 💬 **"Chat with this project"** button opens a new scoped session instantly
- Deleting a project removes all its files, RAG vectors, and cleans up sessions

---

## 🧠 RAG (Built-in)

Store documents as embeddings, search by similarity, and let AI answer questions using your own data. No external vector database required.

### Setup

```bash
ollama pull nomic-embed-text
php artisan migrate
```

```env
AI_RAG_PROVIDER=ollama
AI_RAG_EMBED_MODEL=nomic-embed-text
AI_RAG_CHAT_PROVIDER=ollama
```

### Usage

```php
// Ingest
AI::rag()->ingest('Laravel is a PHP framework using MVC pattern.', 'docs');

// Ask
$answer = AI::rag()->ask('What is Laravel?');

// Search (without AI)
$results = AI::rag()->search('MVC pattern');
// [['content' => '...', 'source' => 'docs', 'score' => 0.91]]

// Scoped to a source
$results = AI::rag()->source('project_5')->search('your query');

// Flush all
AI::rag()->flush();

// Flush one source
AI::rag()->flush('project_5');
```

### Ingest via Artisan

```bash
php artisan ai:rag:ingest storage/docs/manual.txt --source=manual
php artisan ai:rag:ingest storage/docs/ --flush
```

### RAG Configuration

| `.env` Key | Default | Description |
|------------|---------|-------------|
| `AI_RAG_PROVIDER` | `ollama` | Embedding provider |
| `AI_RAG_EMBED_MODEL` | `nomic-embed-text` | Embedding model |
| `AI_RAG_CHAT_PROVIDER` | `null` (uses default) | Chat provider for `ask()` |
| `AI_RAG_CHUNK_SIZE` | `2000` | Max chars per chunk |
| `AI_RAG_TOP_K` | `3` | Chunks retrieved per query |
| `AI_RAG_TABLE` | `ai_documents` | Database table |

---

## 🤖 Providers

### Ollama (Self-Hosted — Free)

```env
AI_PROVIDER=ollama
AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=qwen2:1.5b
```

### OpenAI (ChatGPT)

```env
AI_OPENAI_KEY=sk-your-api-key
AI_OPENAI_MODEL=gpt-4o-mini
```

### Anthropic (Claude)

```env
AI_ANTHROPIC_KEY=sk-ant-your-api-key
AI_ANTHROPIC_MODEL=claude-sonnet-4-20250514
```

### DeepSeek

```env
AI_DEEPSEEK_KEY=sk-your-api-key
AI_DEEPSEEK_MODEL=deepseek-chat
```

---

## ✨ Features

### Fluent Builder API

```php
$response = AI::provider('ollama')
    ->model('qwen2:1.5b')
    ->temperature(0.9)
    ->maxTokens(500)
    ->systemPrompt('You are a helpful Laravel expert.')
    ->chat([['role' => 'user', 'content' => 'Explain middleware']]);
```

### Streaming

```php
AI::provider('ollama')->stream(
    [['role' => 'user', 'content' => 'Write a poem']],
    function (string $chunk) {
        echo $chunk;
    }
);
```

### Health Check

```php
if (AI::provider('ollama')->health()) {
    $response = AI::chat($messages);
}
```

### Token Estimation

```php
$tokens = AI::estimateTokens('Hello world');
```

### Ollama Advanced Features

```php
// JSON output
AI::provider('ollama')->format('json')->chat($messages);

// Embeddings
$vector = AI::provider('ollama')->embed('Hello world');

// Keep model in memory
AI::provider('ollama')->keepAlive('10m')->chat($messages);

// Model management
AI::provider('ollama')->pullModel('llama3.1:8b');
AI::provider('ollama')->runningModels();
AI::provider('ollama')->deleteModel('old-model');
```

### Error Handling

```php
use EasyAI\LaravelAI\Exceptions\ConnectionException;
use EasyAI\LaravelAI\Exceptions\ProviderException;

try {
    $response = AI::provider('openai')->chat($messages);
} catch (ConnectionException $e) {
    Log::error("Connection failed: " . $e->getMessage());
} catch (ProviderException $e) {
    Log::error("Provider error [{$e->getProvider()}]: " . $e->getMessage());
}
```

### Custom Drivers

```php
// AppServiceProvider@boot
AI::extend('groq', function ($config) {
    return new GroqDriver($config);
});
```

---

## 📖 API Reference

### Facade Methods

| Method | Returns | Description |
|--------|---------|-------------|
| `AI::chat(array $messages)` | `AIResponse` | Chat with default provider |
| `AI::provider(string $name)` | `AIProvider` | Switch provider |
| `AI::estimateTokens(string\|array $input)` | `int` | Estimate token count |
| `AI::rag()` | `RAGManager` | Access RAG system |

### Provider Methods (Chainable)

| Method | Description |
|--------|-------------|
| `->model($name)` | Set model |
| `->temperature($float)` | Set creativity (0–2) |
| `->maxTokens($int)` | Max response tokens |
| `->systemPrompt($text)` | Set system instructions |
| `->timeout($seconds)` | Request timeout |
| `->chat(array $messages)` | Send and get response |
| `->stream(array $messages, callable $fn)` | Stream response |
| `->health()` | Check provider online |
| `->models()` | List available models |

### RAG Methods

| Method | Description |
|--------|-------------|
| `->ingest($text, $source)` | Store text as embeddings |
| `->search($query)` | Similarity search |
| `->ask($question)` | RAG-powered Q&A |
| `->source($name)` | Scope to a source |
| `->flush($source?)` | Delete documents |

### Ollama-Only Methods

| Method | Description |
|--------|-------------|
| `->format('json')` | Force JSON output |
| `->embed($text)` | Generate embedding |
| `->keepAlive($duration)` | Keep model in memory |
| `->options($array)` | Raw Ollama options |
| `->pullModel($name)` | Download model |
| `->showModel($name)` | Model details |
| `->deleteModel($name)` | Remove model |
| `->copyModel($src, $dst)` | Copy model |
| `->runningModels()` | List loaded models |

### AIResponse Object

| Property | Type | Description |
|----------|------|-------------|
| `$response->content` | `string` | AI reply text |
| `$response->model` | `string` | Model used |
| `$response->promptTokens` | `int` | Input tokens |
| `$response->replyTokens` | `int` | Output tokens |
| `$response->totalTokens` | `int` | Total tokens |
| `$response->provider` | `string` | Provider name |
| `$response->getRaw()` | `array` | Raw API response |

### Helper Function

```php
ai('Your question')                                     // default provider
ai('Your question', 'openai')                           // specific provider
ai('Your question', 'anthropic', 'claude-haiku-...')   // provider + model
```

---

## ⚙️ Configuration

```php
// config/ai.php (after php artisan vendor:publish --tag=ai-config)
return [
    'default' => env('AI_PROVIDER', 'ollama'),
    'providers' => [
        'ollama'    => ['driver' => 'ollama',    'url' => env('AI_OLLAMA_URL'), ...],
        'openai'    => ['driver' => 'openai',    'api_key' => env('AI_OPENAI_KEY'), ...],
        'anthropic' => ['driver' => 'anthropic', 'api_key' => env('AI_ANTHROPIC_KEY'), ...],
        'deepseek'  => ['driver' => 'deepseek',  'api_key' => env('AI_DEEPSEEK_KEY'), ...],
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
AI_PROVIDER=ollama

AI_OLLAMA_URL=http://127.0.0.1:11434
AI_OLLAMA_MODEL=qwen2:1.5b

AI_OPENAI_KEY=sk-proj-xxxx
AI_OPENAI_MODEL=gpt-4o-mini

AI_ANTHROPIC_KEY=sk-ant-xxxx
AI_ANTHROPIC_MODEL=claude-sonnet-4-20250514

AI_DEEPSEEK_KEY=sk-xxxx
AI_DEEPSEEK_MODEL=deepseek-chat

AI_RAG_PROVIDER=ollama
AI_RAG_EMBED_MODEL=nomic-embed-text
AI_RAG_CHAT_PROVIDER=ollama
AI_RAG_CHUNK_SIZE=2000
AI_RAG_TOP_K=3
```

---

## 🧪 Testing

```bash
vendor/bin/phpunit
```

Uses `Http::fake()` — no real API calls needed.

---

## 🗺️ Roadmap

| Version | Feature | Status |
|---------|---------|--------|
| v1.0 | Ollama, OpenAI, Anthropic, DeepSeek | ✅ Released |
| v1.1 | Laravel 12 & 13 support | ✅ Released |
| v1.2 | Built-in RAG system | ✅ Released |
| v1.3 | Built-in Chat UI | ✅ Released |
| v1.4 | Projects + RAG scoping (self-hosted Claude Projects) | ✅ Released |
| v2.0 | Function / Tool calling | 🔜 Planned |
| v2.0 | Vision / Image input | 🔜 Planned |
| v2.1 | Groq driver | 🔜 Planned |
| v2.1 | Google Gemini driver | 🔜 Planned |
| v2.2 | Response caching | 🔜 Planned |
| v3.0 | Image generation | 🔜 Planned |

---

## ❤️ Support & Donations

[![Buy Me A Coffee](https://img.shields.io/badge/Buy%20Me%20A%20Coffee-FFDD00?style=for-the-badge&logo=buy-me-a-coffee&logoColor=black)](https://buymeacoffee.com/muradbdinfo)
[![GitHub Sponsors](https://img.shields.io/badge/GitHub%20Sponsors-EA4AAA?style=for-the-badge&logo=github-sponsors&logoColor=white)](https://github.com/sponsors/muradbdinfo)

- ⭐ Star this repo on GitHub
- 🐛 Report bugs via [Issues](https://github.com/muradbdinfo/laravelai/issues)
- 📢 Share with your developer friends

---

## 👤 Credits

**Md Murad Hosen** — Full-Stack Laravel Vue Developer and DevOps Engineer from Chittagong, Bangladesh 🇧🇩

| Platform | Link |
|----------|------|
| 🌐 Website | [easyit.com.bd](https://www.easyit.com.bd) |
| 📺 YouTube | [EasyBD IT](https://youtube.com/@easybdit) |
| 📘 Facebook | [Murad Hosen](https://facebook.com/muradhosenofficial) |
| 💻 GitHub | [muradbdinfo](https://github.com/muradbdinfo) |

---

## 📄 License

MIT License — free to use in personal and commercial projects.

Made with ❤️ in Bangladesh 🇧🇩