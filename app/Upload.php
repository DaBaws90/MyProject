<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Upload extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'filename', 'alias', 'path',
    ];

    protected static function boot() {
        parent::boot();

        static::creating(function($upload) {
            $upload->user_id = auth()->id();
        });
    }

    public function owner() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
