<?php namespace App\Models;

use CodeIgniter\Model;

class QuestionOptionModel extends Model
{
    protected $table      = 'QUESTION_OPTIONS';
    protected $primaryKey = 'ID';

    protected $allowedFields = [
        'QUESTION_ID',
        'OPTION_TEXT'
    ];

    public $useTimestamps = false;
}
