<?php namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    // 1) The table name:
    protected $table = 'NOTIFICATIONS';

    // 2) The primary key column:
    protected $primaryKey = 'ID';

    // 3) Let CI handle auto‐increment for you:
    protected $useAutoIncrement = true;

    // 4) Which fields are safe to insert/update
    protected $allowedFields = [
        'USER_ID',
        'MESSAGE',
        'IS_READ',
        'DATE_CREATED',
    ];

    // 5) Return arrays, not objects:
    protected $returnType = 'array';

    // 6) We’re managing timestamps ourselves (via trigger/default), so disable CI’s auto timestamps:
    protected $useTimestamps = false;
}
