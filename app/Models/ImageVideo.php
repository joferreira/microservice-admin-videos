<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageVideo extends Model
{
    use HasFactory;

    protected $table = 'image_videos';

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
