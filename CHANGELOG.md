# Changelog

## v1.3.0 — 2026-05-08

### 🎉 Built-in Chat UI (Zero Setup)

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

**New files added to package:**
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

### Ollama Advanced Features

- JSON mode and structured outputs via `format()`
- Vector embeddings via `embed()`
- Model memory control via `keepAlive()`
- Custom Ollama parameters via `options()`
- Corrected `maxTokens()` mapping to Ollama's `num_predict`
- Model management: `showModel()`, `pullModel()`, `deleteModel()`, `copyModel()`, `runningModels()`
- All 22 tests passing

---

## v1.1.0 — 2026-05-02

### Laravel 12 & 13 Support

- Confirmed compatibility with Laravel 12 and 13
- Updated CI matrix to include PHP 8.3, 8.4
- Orchestra Testbench updated to support testbench `^10.0`

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