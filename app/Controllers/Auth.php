<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Auth extends Controller
{
    public function login()
    {
        helper('form');
        $data = [];

        if ($this->request->getMethod() === 'post') {
            // Step 1: Confirm form values received
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            echo "<pre>Form received:\nEmail: $email\nPassword: $password</pre>";

            // Step 2: Load model and try to authenticate
            $model = new UserModel();
            $user = $model->authenticate($email, $password);

            if ($user) {
                echo "<p style='color:green;'>✔ User found and password matched.</p>";
                
                // Step 3: Set session
                session()->set([
                    'isLoggedIn' => true,
                    'user_id'    => $user['ID'],
                    'user_name'  => $user['FIRSTNAME'] . ' ' . $user['LASTNAME'],
                    'user_type'  => $user['TYPE']
                ]);

                echo "<pre>Session set:\n";
                print_r(session()->get());
                echo "</pre>";

                // Step 4: Try redirect
                echo "<p style='color:blue;'>Redirecting to /survey ...</p>";
                return redirect()->to('/survey');
            } else {
                echo "<p style='color:red;'>❌ Invalid email or password.</p>";
                $data['error'] = 'Invalid email or password';
            }

            // Remove these echo lines once debug is complete
        }

        echo view('auth/login', $data);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Logged out successfully.');
    }
}
