<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'USERSS';
    protected $primaryKey = 'ID';
    protected $returnType = 'array';

    protected $allowedFields = [
        'FIRSTNAME', 'LASTNAME', 'MIDDLENAME',
        'CONTACT', 'ADDRESS',
        'EMAIL', 'PASSWORD', 'TYPE',
        'AGE_GROUP','GENDER','RELIGION','ETHNICITY',
        'MARITAL_STATUS','CHILDREN_COUNT','EDUCATION_LEVEL',
        'JOB_BAND','SERVICE_DURATION','SALARY_RANGE','HOUSEHOLD_INCOME'
    ];

    // disable automatic escaping so Oracle uppercases your table/fields correctly
     protected $escapeIdentifiers = false;
}
