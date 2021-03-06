<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks';

    public function signedStudents() {
        return $this->belongsToMany(User::class, 'user_tasks', 'user_id', 'task_id');
    }
}