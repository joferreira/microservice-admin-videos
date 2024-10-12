<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageVideo extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'path',
        'type',
    ];

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime'
    ];

    protected $table = 'image_videos';

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
