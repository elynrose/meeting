<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'todos';

    protected $dates = [
        'due_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'item',
        'note',
        'due_date',
        'time_due',
        'send_reminder',
        'completed',
        'research',
        'color',
        'research_result',
        'session_id',
        'ordering',
        'priority',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getDueDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('panel.date_format')) : null;
    }

    public function setDueDateAttribute($value)
    {
        $this->attributes['due_date'] = $value ? Carbon::createFromFormat(config('panel.date_format'), $value)->format('Y-m-d') : null;
    }

    public function assigned_tos()
    {
        return $this->belongsToMany(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

   // Assuming there is a relationship defined in the Todo model
    public function assignedUsers()
    {
        return $this->belongsToMany(User::class);
    } 

    
}
