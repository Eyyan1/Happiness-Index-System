<?php namespace App\Models;

use CodeIgniter\Model;

class SectionModel extends Model
{
    protected $table      = 'SECTIONS';
    protected $primaryKey = 'ID';

    protected $allowedFields = [
        'SURVEY_ID',
        'NAME',
        'DESCRIPTION',
        'ORDER_BY'
    ];

    public $useTimestamps = false;
}
