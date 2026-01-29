<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $table = 'attachments';

    protected $fillable = [
        'user_id',
        'path',
        'original_name',
        'mime',
        'size',
        'content_hash',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
