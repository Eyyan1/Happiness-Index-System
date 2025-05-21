<?php namespace App\Models;

use CodeIgniter\Model;

class OptionModel extends Model
{
    protected $table      = 'QUESTION_OPTIONS';
    protected $primaryKey = 'ID';
    protected $allowedFields = ['QUESTION_ID', 'OPTION_TEXT', 'ORDER_NO'];

    public $useAutoIncrement = true;
    public $returnType       = 'array';
}
