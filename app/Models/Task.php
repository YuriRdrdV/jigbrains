<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'start_time',
        'due_time',
        'is_recurring',
        'recurrence_type',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'due_date' => 'datetime',
        'start_time' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function routines()
    {
        return $this->belongsToMany(Routine::class, 'routine_tasks');
    }
}
