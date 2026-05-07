# Changelog

## v1.2.5 — 2026-05-07

- fix: explicitly set chat model in `ask()` to prevent embed model bleeding over from previous `embed()` call
- fix: move `rag` config block outside `providers` array

## v1.2.4 — 2026-05-07

- fix: remove RAGManager constructor — use lazy `config()` calls so config is always available at runtime
- fix: move RAGManager singleton registration to `boot()` instead of `register()`

## v1.2.3 — 2026-05-07

- fix: RAGManager singleton registered too early before config was merged

## v1.2.2 — 2026-05-07

### Built-in RAG System 🧠

- **`AI::rag()->ingest(string $content, string $source)`** — chunk text and store as embeddings
- **`AI::rag()->ask(string $question)`** — answer questions using retrieved context
- **`AI::rag()->search(string $query)`** — return top-K most relevant chunks with cosine similarity scores
- **`AI::rag()->flush()`** — clear all stored documents
- **`php artisan ai:rag:ingest {path}`** — ingest files or directories from CLI
- Auto-migration for `ai_documents` table via `loadMigrationsFrom`
- Cosine similarity search (no external vector DB required — works with MySQL/SQLite)
- All RAG settings configurable via `.env` (`AI_RAG_PROVIDER`, `AI_RAG_EMBED_MODEL`, `AI_RAG_CHUNK_SIZE`, `AI_RAG_TOP_K`, `AI_RAG_TABLE`)

## v1.2.1 — 2026-05-04

- fix: restore AI Facade class (was overwritten by config content)
- fix: composer.json PHP/Laravel version constraints

## v1.2.0 — 2026-05-04

### Ollama Enrichment Features

- **Embeddings** — `->embed(string|array $input)` generates vector embeddings via Ollama `/api/embed`
- **JSON mode** — `->format('json')` forces structured JSON output
- **Structured outputs** — `->format(array $schema)` enforces a JSON schema
- **Keep-alive** — `->keepAlive('5m')` controls how long model stays loaded in memory
- **Custom options** — `->options(['num_ctx' => 4096])` passes any Ollama parameter
- **Correct token mapping** — `maxTokens()` now maps to `num_predict` (Ollama's correct field)
- **Model management** — `showModel()`, `pullModel()`, `deleteModel()`, `copyModel()`, `runningModels()`

## v1.1.2 — 2026-05-02

- Laravel 13 support added
- PHP 8.4 / 8.5 explicit support
- README updated with video tutorials

## v1.1.1 — 2026-05-01

- Minor README and SEO improvements

## v1.1.0 — 2026-05-01

- Laravel 12 support
- Improved error handling with `ConnectionException` and `ProviderException`
- Health check improvements

## v1.0.0 — 2026-05-01

- Initial release
- Ollama driver (chat, stream, health, models)
- OpenAI driver (ChatGPT)
- Anthropic driver (Claude)
- DeepSeek driver (OpenAI-compatible)
- Unified `AIResponse` object
- Token estimation
- Streaming support (SSE) for all providers
- Laravel Facade (`AI::`) + global `ai()` helper
- Custom driver extension via `AI::extend()`
- Config publishing via `vendor:publish --tag=ai-config`
- PHPUnit tests with HTTP mocking via Orchestra Testbench