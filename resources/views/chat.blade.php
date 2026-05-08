<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI Chat — LaravelAI</title>

    {{-- Offline-safe local assets (publish with: php artisan vendor:publish --tag=ai-chat-assets) --}}
    <link rel="stylesheet" href="{{ asset('vendor/laravelai/css/github-dark.min.css') }}">
    <script src="{{ asset('vendor/laravelai/js/marked.min.js') }}"></script>
    <script src="{{ asset('vendor/laravelai/js/highlight.min.js') }}"></script>

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --sidebar-bg:     #0f1117;
            --sidebar-border: #1e2433;
            --sidebar-text:   #8b95a8;
            --sidebar-hover:  #161c2d;
            --sidebar-active: #1a2035;
            --accent:         #6366f1;
            --accent-hover:   #4f46e5;
            --surface:        #ffffff;
            --bg:             #f5f6fa;
            --border:         #e8eaef;
            --text:           #1a1d27;
            --text-muted:     #8b95a8;
            --radius:         12px;
            --shadow:         0 1px 3px rgba(0,0,0,0.07), 0 4px 12px rgba(0,0,0,0.04);
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            height: 100vh;
            display: flex;
            overflow: hidden;
            color: var(--text);
        }

        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }

        /* ─── SIDEBAR ─────────────────────────────── */
        .sidebar {
            width: 260px; min-width: 260px;
            background: var(--sidebar-bg);
            display: flex; flex-direction: column;
            height: 100vh;
            border-right: 1px solid var(--sidebar-border);
        }
        .sidebar-top { padding: 18px 14px 12px; }

        .app-brand { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; }
        .brand-icon {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
        }
        .brand-name { font-size: 0.88rem; font-weight: 600; color: #e2e8f0; }
        .brand-sub  { font-size: 0.68rem; color: var(--sidebar-text); }

        .provider-select {
            width: 100%;
            background: #161c2d;
            border: 1px solid var(--sidebar-border);
            color: #c8d0de;
            padding: 8px 28px 8px 10px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-family: inherit;
            cursor: pointer;
            margin-bottom: 8px;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236b7280'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
        }
        .provider-select:focus { outline: none; border-color: var(--accent); }
        .provider-select option { background: #161c2d; }

        .provider-badge {
            font-size: 0.7rem; color: #3d4659;
            padding: 0 2px 10px;
            display: flex; align-items: center; gap: 6px;
        }
        .provider-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #10b981; box-shadow: 0 0 6px #10b981;
            animation: pulse 2s infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

        .new-chat-btn {
            width: 100%; padding: 9px 14px;
            background: var(--accent); color: white;
            border: none; border-radius: 8px;
            font-size: 0.85rem; font-weight: 500;
            cursor: pointer; font-family: inherit;
            display: flex; align-items: center; gap: 8px;
            transition: background 0.15s;
        }
        .new-chat-btn:hover { background: var(--accent-hover); }

        .sessions-label {
            padding: 14px 18px 6px;
            font-size: 0.67rem; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: #2d3548;
        }

        .session-list { flex: 1; overflow-y: auto; padding: 2px 8px; }

        .session-item {
            display: flex; align-items: center;
            padding: 8px 10px; border-radius: 7px;
            cursor: pointer; font-size: 0.82rem;
            color: var(--sidebar-text);
            text-decoration: none;
            margin-bottom: 1px; gap: 8px;
            transition: background 0.1s, color 0.1s;
        }
        .session-item:hover  { background: var(--sidebar-hover); color: #c8d0de; }
        .session-item.active { background: var(--sidebar-active); color: #e2e8f0; }
        .session-title { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .delete-btn {
            background: none; border: none; color: transparent;
            cursor: pointer; padding: 2px 5px; border-radius: 4px;
            font-size: 0.7rem; flex-shrink: 0; transition: all 0.15s;
        }
        .session-item:hover .delete-btn { color: #3d4659; }
        .delete-btn:hover { background: #ef4444 !important; color: white !important; }

        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid var(--sidebar-border);
            font-size: 0.7rem; color: #2d3548;
        }
        .sidebar-footer a { color: #4a5568; text-decoration: none; }
        .sidebar-footer a:hover { color: var(--accent); }

        /* ─── MAIN ────────────────────────────────── */
        .main { flex: 1; display: flex; flex-direction: column; height: 100vh; overflow: hidden; }

        .chat-header {
            padding: 13px 22px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
            box-shadow: var(--shadow); z-index: 10;
        }
        .header-icon {
            width: 28px; height: 28px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }
        .header-title { font-size: 0.9rem; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .header-meta  { margin-left: auto; display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .header-provider {
            font-size: 0.72rem; color: var(--text-muted);
            background: var(--bg); border: 1px solid var(--border);
            padding: 3px 8px; border-radius: 20px;
        }
        .msg-count { font-size: 0.72rem; color: var(--text-muted); }

        /* ─── MESSAGES ────────────────────────────── */
        #messages {
            flex: 1; overflow-y: auto;
            padding: 28px 22px;
            display: flex; flex-direction: column; gap: 20px;
        }

        .welcome { margin: auto; text-align: center; max-width: 420px; }
        .welcome-icon {
            width: 68px; height: 68px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 30px; margin: 0 auto 20px;
            box-shadow: 0 8px 32px rgba(99,102,241,0.2);
        }
        .welcome h2 { font-size: 1.25rem; font-weight: 600; margin-bottom: 8px; }
        .welcome p  { font-size: 0.87rem; color: var(--text-muted); line-height: 1.65; }
        .welcome-caps { margin-top: 18px; display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
        .cap-chip {
            background: var(--bg); border: 1px solid var(--border);
            padding: 4px 11px; border-radius: 20px;
            font-size: 0.74rem; color: var(--text-muted);
        }

        .msg-row { display: flex; gap: 12px; align-items: flex-start; animation: msgIn 0.18s ease; }
        .msg-row.user { flex-direction: row-reverse; }
        @keyframes msgIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:none} }

        .avatar {
            width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem; font-weight: 600;
            flex-shrink: 0; margin-top: 2px;
        }
        .avatar.user { background: var(--accent); color: white; }
        .avatar.ai   { background: linear-gradient(135deg,#6366f1,#8b5cf6); color: white; }

        .bubble-wrap { max-width: 72%; display: flex; flex-direction: column; gap: 4px; }
        .msg-row.user .bubble-wrap { align-items: flex-end; }

        .bubble {
            padding: 10px 14px; border-radius: 12px;
            font-size: 0.9rem; line-height: 1.6; word-break: break-word;
        }
        .bubble.user { background: var(--accent); color: white; border-bottom-right-radius: 3px; }
        .bubble.ai   { background: var(--surface); color: var(--text); border: 1px solid var(--border); border-bottom-left-radius: 3px; box-shadow: var(--shadow); }

        .bubble.streaming .md-body::after {
            content: '▋'; animation: blink 0.6s step-end infinite; color: var(--accent);
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }

        .bubble-actions { display: flex; gap: 6px; opacity: 0; transition: opacity 0.15s; }
        .bubble-wrap:hover .bubble-actions { opacity: 1; }
        .copy-btn {
            background: var(--surface); border: 1px solid var(--border);
            color: var(--text-muted); padding: 3px 8px; border-radius: 5px;
            font-size: 0.7rem; cursor: pointer; font-family: inherit; transition: all 0.15s;
        }
        .copy-btn:hover { border-color: var(--accent); color: var(--accent); }
        .copy-btn.copied { color: #10b981; border-color: #10b981; }

        /* ─── MARKDOWN ────────────────────────────── */
        .md-body { line-height: 1.7; }
        .md-body p { margin-bottom: 10px; }
        .md-body p:last-child { margin-bottom: 0; }
        .md-body h1,.md-body h2,.md-body h3,.md-body h4 { font-weight: 600; margin: 14px 0 7px; }
        .md-body h1 { font-size: 1.2rem; } .md-body h2 { font-size: 1.05rem; } .md-body h3 { font-size: 0.97rem; }
        .md-body ul,.md-body ol { padding-left: 20px; margin-bottom: 10px; }
        .md-body li { margin-bottom: 3px; }
        .md-body li::marker { color: var(--accent); }
        .md-body blockquote {
            border-left: 3px solid var(--accent); padding: 6px 14px; margin: 10px 0;
            background: rgba(99,102,241,0.05); border-radius: 0 6px 6px 0;
            color: var(--text-muted); font-style: italic;
        }
        .md-body a { color: var(--accent); text-decoration: none; }
        .md-body a:hover { text-decoration: underline; }
        .md-body table { width: 100%; border-collapse: collapse; margin: 12px 0; font-size: 0.86rem; }
        .md-body th { background: var(--bg); padding: 7px 11px; text-align: left; font-weight: 600; border: 1px solid var(--border); }
        .md-body td { padding: 6px 11px; border: 1px solid var(--border); }
        .md-body tr:nth-child(even) td { background: rgba(0,0,0,0.015); }
        .md-body hr { border: none; border-top: 1px solid var(--border); margin: 14px 0; }
        .md-body strong { font-weight: 600; }
        .md-body code:not(pre code) {
            font-family: 'Cascadia Code','Consolas','Courier New',monospace;
            font-size: 0.82em; background: rgba(99,102,241,0.08); color: #7c3aed;
            padding: 1px 5px; border-radius: 4px; border: 1px solid rgba(99,102,241,0.12);
        }
        .bubble.user .md-body code:not(pre code) {
            background: rgba(255,255,255,0.15); color: rgba(255,255,255,0.95);
            border-color: rgba(255,255,255,0.2);
        }
        .md-body pre { margin: 10px 0; border-radius: 8px; overflow: hidden; border: 1px solid #1e2433; }
        .code-header {
            background: #161c2d; padding: 6px 12px;
            display: flex; align-items: center; justify-content: space-between;
            font-size: 0.7rem; color: #6b7280;
            font-family: 'Cascadia Code','Consolas',monospace;
        }
        .code-copy {
            background: none; border: 1px solid #2d3548; color: #6b7280;
            padding: 1px 7px; border-radius: 4px; font-size: 0.68rem;
            cursor: pointer; font-family: inherit; transition: all 0.15s;
        }
        .code-copy:hover { border-color: var(--accent); color: var(--accent); }
        .code-copy.copied { color: #10b981; border-color: #10b981; }
        .md-body pre code {
            font-family: 'Cascadia Code','Consolas','Courier New',monospace;
            font-size: 0.82rem; padding: 13px !important;
            display: block; overflow-x: auto; line-height: 1.6;
        }

        /* ─── INPUT ───────────────────────────────── */
        .input-wrap { padding: 14px 22px 18px; background: var(--surface); border-top: 1px solid var(--border); }
        .input-box {
            display: flex; gap: 10px; align-items: flex-end;
            background: var(--bg); border: 1.5px solid var(--border);
            border-radius: var(--radius); padding: 9px 12px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .input-box:focus-within { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }
        textarea {
            flex: 1; background: none; border: none; outline: none;
            font-size: 0.91rem; font-family: inherit; resize: none;
            max-height: 160px; line-height: 1.55; color: var(--text);
        }
        textarea::placeholder { color: var(--text-muted); }
        .send-btn {
            background: var(--accent); color: white; border: none;
            width: 36px; height: 36px; border-radius: 8px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem; flex-shrink: 0;
            transition: background 0.15s, transform 0.1s;
        }
        .send-btn:hover:not(:disabled) { background: var(--accent-hover); transform: scale(1.05); }
        .send-btn:disabled { opacity: 0.35; cursor: not-allowed; transform: none; }
        .input-hint {
            display: flex; justify-content: space-between;
            margin-top: 7px; font-size: 0.69rem; color: var(--text-muted); padding: 0 2px;
        }
        .input-hint kbd {
            background: var(--bg); border: 1px solid var(--border); border-radius: 3px;
            padding: 1px 4px; font-family: 'Cascadia Code','Consolas',monospace; font-size: 0.67rem;
        }

        /* No session */
        .no-session {
            flex: 1; display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 14px; color: var(--text-muted);
        }
        .no-session-icon {
            width: 70px; height: 70px;
            background: linear-gradient(135deg,rgba(99,102,241,0.1),rgba(139,92,246,0.1));
            border-radius: 22px; display: flex; align-items: center; justify-content: center;
            font-size: 30px; border: 1px solid rgba(99,102,241,0.15);
        }
        .no-session h2 { font-size: 1.05rem; font-weight: 600; color: #64748b; }
        .no-session p  { font-size: 0.84rem; }
    </style>
</head>
<body>

{{-- ─── SIDEBAR ──────────────────────────────────── --}}
<aside class="sidebar">
    <div class="sidebar-top">
        <div class="app-brand">
            <div class="brand-icon">🤖</div>
            <div>
                <div class="brand-name">LaravelAI Chat</div>
                <div class="brand-sub">muradbdinfo/laravelai</div>
            </div>
        </div>

        <select class="provider-select" id="providerSelect" onchange="switchProvider(this.value)">
            @foreach($providers as $key => $info)
                <option value="{{ $key }}" {{ $activeProvider === $key ? 'selected' : '' }}>
                    {{ $info['icon'] }} {{ $info['label'] }}
                </option>
            @endforeach
        </select>

        <div class="provider-badge">
            <span class="provider-dot"></span>
            Active: <strong style="color:#4a5568">{{ $providers[$activeProvider]['label'] }}</strong>
        </div>

        <form action="{{ route('ai-chat.sessions.new') }}" method="POST">
            @csrf
            <button class="new-chat-btn" type="submit">＋ New Chat</button>
        </form>
    </div>

    <div class="sessions-label">Conversations</div>

    <div class="session-list">
        @forelse($sessions as $session)
            <a href="{{ route('ai-chat.index', ['session' => $session->id]) }}"
               class="session-item {{ $activeSession?->id === $session->id ? 'active' : '' }}">
                <span style="font-size:0.75rem;opacity:0.5">💬</span>
                <span class="session-title">{{ $session->title }}</span>
                <form action="{{ route('ai-chat.sessions.delete', $session) }}" method="POST"
                      onsubmit="return confirm('Delete this chat?')">
                    @csrf @method('DELETE')
                    <button class="delete-btn" type="submit" title="Delete">✕</button>
                </form>
            </a>
        @empty
            <div style="padding:18px 14px;font-size:0.8rem;color:#2d3548;text-align:center;line-height:1.6;">
                No conversations yet.<br>
                Click <strong style="color:#4a5568">+ New Chat</strong> to begin.
            </div>
        @endforelse
    </div>

    <div class="sidebar-footer">
        ⚡ <a href="https://packagist.org/packages/muradbdinfo/laravelai" target="_blank">muradbdinfo/laravelai</a>
    </div>
</aside>

{{-- ─── MAIN ─────────────────────────────────────── --}}
<main class="main">
    @if($activeSession)

        <div class="chat-header">
            <div class="header-icon">💬</div>
            <div class="header-title">{{ $activeSession->title }}</div>
            <div class="header-meta">
                <span class="header-provider">
                    {{ $providers[$activeProvider]['icon'] }} {{ $providers[$activeProvider]['label'] }}
                </span>
                <span class="msg-count">{{ $messages->count() }} msgs</span>
            </div>
        </div>

        <div id="messages">
            @if($messages->isEmpty())
                <div class="welcome">
                    <div class="welcome-icon">🤖</div>
                    <h2>How can I help you?</h2>
                    <p>Ask me anything. Responses render with full <strong>Markdown</strong> —
                       headings, lists, code blocks, tables, and more.</p>
                    <div class="welcome-caps">
                        <span class="cap-chip">📝 Markdown</span>
                        <span class="cap-chip">💻 Syntax highlight</span>
                        <span class="cap-chip">💾 DB history</span>
                        <span class="cap-chip">🔄 4 providers</span>
                    </div>
                </div>
            @else
                @foreach($messages as $msg)
                    <div class="msg-row {{ $msg->role }}">
                        <div class="avatar {{ $msg->role }}">
                            {{ $msg->role === 'user' ? 'You' : 'AI' }}
                        </div>
                        <div class="bubble-wrap">
                            <div class="bubble {{ $msg->role }}">
                                @if($msg->role === 'assistant')
                                    <div class="md-body" data-raw="{{ e($msg->content) }}"></div>
                                @else
                                    <div class="md-body">{{ $msg->content }}</div>
                                @endif
                            </div>
                            @if($msg->role === 'assistant')
                                <div class="bubble-actions">
                                    <button class="copy-btn"
                                        onclick="copyText(this, {{ json_encode($msg->content) }})">Copy</button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="input-wrap">
            <div class="input-box">
                <textarea id="input" rows="1"
                    placeholder="Message {{ $providers[$activeProvider]['label'] }}…"></textarea>
                <button class="send-btn" id="sendBtn" onclick="sendMessage()" title="Send">▶</button>
            </div>
            <div class="input-hint">
                <span>Markdown rendered · History saved to DB</span>
                <span><kbd>Ctrl</kbd>+<kbd>Enter</kbd> to send</span>
            </div>
        </div>

    @else

        <div class="no-session">
            <div class="no-session-icon">💬</div>
            <h2>Select a conversation</h2>
            <p>Choose from the sidebar or start a new chat.</p>
        </div>

    @endif
</main>

<script>
// ─── MARKED SETUP ──────────────────────────────────
const renderer = new marked.Renderer();
renderer.code  = function(code, lang) {
    const hl = (lang && hljs.getLanguage(lang))
        ? hljs.highlight(code, { language: lang }).value
        : hljs.highlightAuto(code).value;
    const label   = lang || 'code';
    const escaped = code.replace(/\\/g,'\\\\').replace(/`/g,'&#96;').replace(/'/g,"\\'");
    return `<div>
        <div class="code-header">
            <span>${label}</span>
            <button class="code-copy" onclick="copyCode(this,'${escaped}')">Copy</button>
        </div>
        <pre><code class="hljs">${hl}</code></pre>
    </div>`;
};
marked.use({ renderer, breaks: true, gfm: true });

function md(text) { return marked.parse(text || ''); }

// Render existing DB messages on page load
document.querySelectorAll('.md-body[data-raw]').forEach(el => {
    el.innerHTML = md(el.dataset.raw);
    delete el.dataset.raw;
});

// ─── COPY HELPERS ──────────────────────────────────
function copyText(btn, text) {
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Copied!'; btn.classList.add('copied');
        setTimeout(() => { btn.textContent = 'Copy'; btn.classList.remove('copied'); }, 2000);
    });
}
function copyCode(btn, escaped) {
    const text = escaped.replace(/&#96;/g, '`');
    navigator.clipboard.writeText(text).then(() => {
        btn.textContent = 'Copied!'; btn.classList.add('copied');
        setTimeout(() => { btn.textContent = 'Copy'; btn.classList.remove('copied'); }, 2000);
    });
}

// ─── PROVIDER SWITCH ───────────────────────────────
const csrf = document.querySelector('meta[name="csrf-token"]').content;

async function switchProvider(value) {
    await fetch('{{ route("ai-chat.provider") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ provider: value })
    });
    location.reload();
}

@if($activeSession)
const SESSION_ID = {{ $activeSession->id }};
let isStreaming  = false;
let streamBuffer = '';

scrollBottom();

async function sendMessage() {
    const input = document.getElementById('input');
    const msg   = input.value.trim();
    if (!msg || isStreaming) return;

    appendUserBubble(msg);
    input.value = '';
    input.style.height = 'auto';
    setLoading(true);

    const aiBubble = appendAIBubble();
    streamBuffer   = '';

    try {
        const params   = new URLSearchParams({ message: msg, session_id: SESSION_ID });
        const response = await fetch(`{{ route('ai-chat.stream') }}?${params}`, {
            headers: { 'X-CSRF-TOKEN': csrf }
        });

        const reader  = response.body.getReader();
        const decoder = new TextDecoder();
        let buf       = '';

        while (true) {
            const { done, value } = await reader.read();
            if (done) break;

            buf += decoder.decode(value, { stream: true });
            const lines = buf.split('\n');
            buf         = lines.pop();

            for (const line of lines) {
                if (!line.startsWith('data: ')) continue;
                const raw = line.slice(6).trim();
                if (raw === '[DONE]') break;
                try {
                    const json = JSON.parse(raw);
                    if (json.error) {
                        aiBubble.innerHTML = `<div class="md-body">❌ ${json.error}</div>`;
                        aiBubble.classList.remove('streaming');
                        break;
                    }
                    if (json.text) {
                        streamBuffer += json.text;
                        aiBubble.querySelector('.md-body').innerHTML = md(streamBuffer);
                        scrollBottom();
                    }
                } catch {}
            }
        }
    } catch (err) {
        aiBubble.innerHTML = `<div class="md-body">❌ ${err.message}</div>`;
    }

    aiBubble.classList.remove('streaming');
    aiBubble.querySelector('.md-body').innerHTML = md(streamBuffer);

    const wrap = aiBubble.closest('.bubble-wrap');
    if (wrap && streamBuffer) {
        const acts = document.createElement('div');
        acts.className = 'bubble-actions';
        acts.innerHTML = `<button class="copy-btn" onclick="copyText(this,${JSON.stringify(streamBuffer)})">Copy</button>`;
        wrap.appendChild(acts);
    }

    scrollBottom();
    setLoading(false);

    const t = document.querySelector('.session-item.active .session-title');
    if (t && t.textContent.trim() === 'New Chat') location.reload();
}

function appendUserBubble(text) {
    document.querySelector('.welcome')?.remove();
    const row = document.createElement('div');
    row.className = 'msg-row user';
    row.innerHTML = `
        <div class="avatar user">You</div>
        <div class="bubble-wrap">
            <div class="bubble user"><div class="md-body">${escHtml(text)}</div></div>
        </div>`;
    document.getElementById('messages').appendChild(row);
    scrollBottom();
}

function appendAIBubble() {
    const row = document.createElement('div');
    row.className = 'msg-row ai';
    row.innerHTML = `
        <div class="avatar ai">AI</div>
        <div class="bubble-wrap">
            <div class="bubble ai streaming">
                <div class="md-body"><em style="opacity:0.35;font-size:0.85rem">Thinking…</em></div>
            </div>
        </div>`;
    document.getElementById('messages').appendChild(row);
    scrollBottom();
    return row.querySelector('.bubble.ai');
}

function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function setLoading(s) {
    isStreaming = s;
    document.getElementById('sendBtn').disabled    = s;
    document.getElementById('sendBtn').textContent = s ? '…' : '▶';
}
function scrollBottom() {
    const m = document.getElementById('messages');
    m.scrollTop = m.scrollHeight;
}

document.getElementById('input').addEventListener('keydown', e => {
    if (e.ctrlKey && e.key === 'Enter') { e.preventDefault(); sendMessage(); }
});
document.getElementById('input').addEventListener('input', function () {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 160) + 'px';
});
@endif
</script>
</body>
</html>
