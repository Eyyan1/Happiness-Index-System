<?php namespace App\Models;
namespace App\Models;
use CodeIgniter\Model;

class SectionModel extends Model
{
    protected $table = 'SECTIONS';
    protected $primaryKey = 'ID';
    protected $allowedFields = ['SURVEY_ID', 'NAME', 'ORDER_NO'];
    public $useTimestamps = false;
}
