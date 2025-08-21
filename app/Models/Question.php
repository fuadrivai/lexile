<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $hidden = [
        'correct_answer_text',
        'correct_answer',
        'passage_id'
    ];
}
