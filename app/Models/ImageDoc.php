<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageDoc extends Model
{
    use HasFactory;
    protected $table = 'image_docs';
    protected $fillable = [
        'user_id',
        'file_name',
        'file_type',
        'file_ext'
    ];
}
