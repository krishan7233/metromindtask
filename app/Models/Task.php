<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'assignedTo',
        'createdBy',
    ];

    // Relationship: Task belongs to assigned user
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assignedTo');
    }

    // Relationship: Task belongs to creator user
    public function creatorUser()
    {
        return $this->belongsTo(User::class, 'createdBy');
    }
}
