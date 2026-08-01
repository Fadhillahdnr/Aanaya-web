<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasUlids, SoftDeletes;

    protected $table = 'media';

    protected $fillable = [
        'mediable_type', 'mediable_id', 'uploaded_by', 'provider', 'public_id',
        'resource_type', 'media_type', 'purpose', 'original_name', 'format',
        'mime_type', 'size_bytes', 'width', 'height', 'duration', 'secure_url',
        'thumbnail_url', 'status', 'visibility', 'sort_order', 'metadata',
        'error_message', 'uploaded_at', 'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'uploaded_at' => 'datetime',
            'processed_at' => 'datetime',
            'duration' => 'decimal:2',
        ];
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
