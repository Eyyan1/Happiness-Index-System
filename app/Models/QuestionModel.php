<?php namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table      = 'QUESTIONS';
    protected $primaryKey = 'ID';

    protected $allowedFields = [
        'QUESTION',
        'TYPE',
        'ORDER_BY',
        'SURVEY_ID',
        'DATE_CREATED'
    ];

    public $useTimestamps = false;
}