<?php

namespace EasyAI\LaravelAI\Chat\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    protected $fillable = ['title'];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function toAIMessages(): array
    {
        return $this->messages->map(fn($m) => [
            'role'    => $m->role,
            'content' => $m->content,
        ])->toArray();
    }
}
