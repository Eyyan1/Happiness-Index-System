<?php namespace App\Models;

use CodeIgniter\Model;

class SurveyModel extends Model
{
    protected $table      = 'SURVEY_SETS';
    protected $primaryKey = 'ID';
    protected $allowedFields = [
        'TITLE',
        'DESCRIPTION',
        'USER_ID',
        'START_DATE',
        'END_DATE',
    ];
}
