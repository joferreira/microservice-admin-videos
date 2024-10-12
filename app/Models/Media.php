<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory, UuidTrait;

    protected $fillable = [
        'file_path',
        'encoded_path',
        'media_status',
        'type',
    ];

    public $incrementing = false;

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime'
    ];

    protected $table = 'medias_video';

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
