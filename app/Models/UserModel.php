<?php namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'USERS';
    protected $primaryKey = 'ID';
    protected $allowedFields = ['FIRSTNAME', 'LASTNAME', 'EMAIL', 'PASSWORD', 'TYPE', 'DATE_CREATED'];

    public function authenticate($email, $password)
    {
        $user = $this->where('EMAIL', $email)->first();
    
        if ($user) {
            echo "DB Password: " . $user['PASSWORD'] . "<br>";
            echo "Input Password: " . $password . "<br>";
    
            if (password_verify($password, $user['PASSWORD'])) {
                echo "✔ password verified";
                return $user;
            } else {
                echo "❌ password mismatch";
            }
        } else {
            echo "❌ user not found";
        }
    
        exit;
    }
}    
