<?php namespace App\Models;
use CodeIgniter\Model;

class AnswerModel extends Model
{
    protected $table      = 'ANSWERS';        // ← UPPERCASE!
    protected $primaryKey = 'ID';
    protected $allowedFields = [
      'SURVEY_ID','USER_ID','QUESTION_ID','ANSWER'
    ];
}
