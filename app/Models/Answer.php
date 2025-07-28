<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $hidden = [
        'student_id',
        'passage_id',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
    ];

    public function details()
    {
        return $this->hasMany(AnswerDetail::class);
    }
    public function passage()
    {
        return $this->belongsTo(Passage::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
