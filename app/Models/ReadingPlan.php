<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingPlan extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'target_date',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'target_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
