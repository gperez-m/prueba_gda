<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSessions extends Model
{
    use HasFactory;

    protected $table = 'log_sessions';
    protected $fillable = ['id', 'user_id', 'ip'];
}
