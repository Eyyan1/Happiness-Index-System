<?php namespace App\Models;

use CodeIgniter\Model;

class AnswerModel extends Model
{
    protected $table      = 'ANSWERS';
    protected $primaryKey = 'ID';

    protected $allowedFields = [
        'SURVEY_ID',
        'QUESTION_ID',
        'USER_ID',     // ← Make sure you added this
        'ANSWER_TEXT',
        'DATE_CREATED'
    ];

    protected $useTimestamps = false;  // adjust if you’re not using CI’s timestamp feature
}
