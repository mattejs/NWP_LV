<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectUsers extends Model
{
    protected $fillable = ['group_id', 'user_id'];
}