# Changelog

## v1.4.0 — 2026-05-10

### 🗂️ Projects + RAG Scoping (Self-hosted Claude Projects)

A full Projects system built into the Chat UI — create knowledge bases, upload documents, and chat with RAG-powered context scoped per project. Mirrors how Claude.ai Projects works, fully self-hosted.

**New features:**
- **Projects sidebar** — create, open, and delete projects from the Chat UI
- **File manager per project** — upload `.txt`, `.md`, `.pdf` files; auto-ingested into RAG on upload
- **Scoped RAG** — `AI::rag()->source('project_5')->search($query)` retrieves only that project's documents
- **RAG auto-injection** — project chat sessions automatically prepend RAG context as system message before streaming
- **RAG badge** — header shows `🧠 RAG ON` when chatting inside a project session
- **Project-aware sessions** — sessions linked to a project via `project_id`; normal sessions unaffected
- **Safe project delete** — deletes RAG vectors, files, and sessions cleanly
- **PDF support** — optional via `composer require smalot/pdfparser`

**New package files:**
- `src/Chat/Models/Project.php`
- `src/Chat/Models/ProjectFile.php`
- `src/Chat/Controllers/ProjectController.php`
- `src/Chat/Controllers/ProjectFileController.php`
- `database/migrations/2026_05_10_000000_create_projects_tables.php`

**Updated files:**
- `src/RAG/RAGManager.php` — added `source()`, scoped `search()`, scoped `flush()`
- `src/Chat/Models/ChatSession.php` — added `project_id` fillable + `project()` relation
- `src/Chat/Controllers/AIChatController.php` — RAG injection in stream, project-aware index, JSON session creation
- `routes/chat.php` — project and file routes added
- `resources/views/chat.blade.php` — full Projects UI, file manager modal, RAG indicators

**RAG API additions:**
```php
// Scope search to a project
AI::rag()->source('project_5')->search($query);
AI::rag()->source('project_5')->ask($question);

// Scoped flush (delete only one project's vectors)
AI::rag()->flush('project_5');
```

---

## v1.3.0 — 2026-05-08

### 💬 Built-in Chat UI (Zero Setup)

Install the package and get a full ChatGPT-like chat application automatically — no controller, no routes, no views to create manually.

**New features:**
- Built-in chat UI accessible at `/ai-chat` after install
- ChatGPT-like sidebar with session management (create, switch, delete)
- Full Markdown rendering with syntax-highlighted code blocks
- Streaming responses with real-time typing effect
- Per-message and per-code-block copy buttons
- Live provider switcher — switch between Ollama, OpenAI, Claude, DeepSeek from the UI
- Database-persisted conversation history (survives page refresh)
- Auto-title: first message becomes the session title automatically
- Offline-safe: JS/CSS assets published locally (no CDN dependency)
- Views publishable for full customization: `vendor:publish --tag=ai-chat-views`
- Assets publishable for offline use: `vendor:publish --tag=ai-chat-assets`
- Migrations load automatically via `ChatServiceProvider`
- All routes prefixed `/ai-chat/api/` to avoid conflicts with app routes

**New files:**
- `src/Chat/ChatServiceProvider.php`
- `src/Chat/Controllers/AIChatController.php`
- `src/Chat/Models/ChatSession.php`
- `src/Chat/Models/ChatMessage.php`
- `routes/chat.php`
- `resources/views/chat.blade.php`
- `database/migrations/2024_01_01_000000_create_chat_tables.php`
- `public/js/marked.min.js`
- `public/js/highlight.min.js`
- `public/css/github-dark.min.css`

---

## v1.2.0 — 2026-05-03

### 🧠 Built-in RAG System + Ollama Advanced Features

**RAG:**
- `AI::rag()->ingest($text, $source)` — store documents as embeddings
- `AI::rag()->search($query)` — cosine similarity search
- `AI::rag()->ask($question)` — RAG-powered Q&A
- `AI::rag()->flush()` — clear all documents
- `php artisan ai:rag:ingest` — ingest from CLI
- No external vector DB required — uses your existing SQL database

**Ollama Advanced:**
- JSON mode and structured outputs via `format()`
- Vector embeddings via `embed()`
- Model memory control via `keepAlive()`
- Custom Ollama parameters via `options()`
- Corrected `maxTokens()` mapping to Ollama's `num_predict`
- Model management: `showModel()`, `pullModel()`, `deleteModel()`, `copyModel()`, `runningModels()`

---

## v1.1.0 — 2026-05-02

### Laravel 12 & 13 Support

- Confirmed compatibility with Laravel 12 and 13
- Updated CI matrix to include PHP 8.3, 8.4
- Orchestra Testbench updated to support `^10.0`

---

## v1.0.0 — 2026-05-01

### Initial Release

- Ollama driver (chat, stream, health, models)
- OpenAI driver (ChatGPT)
- Anthropic driver (Claude)
- DeepSeek driver (OpenAI-compatible)
- Unified `AIResponse` object
- Token estimation
- Streaming support for all providers
- Laravel Facade + global `ai()` helper
- Custom driver extension support
- Config publishing
- PHPUnit tests with HTTP mocking