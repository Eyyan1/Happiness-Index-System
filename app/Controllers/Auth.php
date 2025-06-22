<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class Auth extends Controller
{
    // Show login form
    public function loginForm()
    {
        return view('auth/login', [
        'hideNavbar'  => true,
        'hideSidebar' => true, 
    ]);
    }

    // Handle login POST
    public function login()
    {
        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $model = new UserModel();
        $user  = $model->where('EMAIL', $email)->first();

        if (! $user || ! password_verify($password, $user['PASSWORD'])) {
            return redirect()->back()->with('error', 'Invalid credentials')->withInput();
        }

        // e.g. 1 = admin, anything else = regular user
        $rawType = (int) $user['TYPE'];
        $role    = $rawType === 1 ? 'admin' : 'user';

            // Map numeric TYPE → a textual role
        $role = $user['TYPE'] == 1 ? 'admin' : 'user';

        session()->set([
            'isLoggedIn' => true,
            'userId'     => $user['ID'],
            'role'       => $role,
            'name'       => $user['FIRSTNAME'].' '.$user['LASTNAME'],
        ]);

        if (! $user || ! password_verify($password, $user['PASSWORD'])) {
        return redirect()->back()
                         ->with('error', 'Invalid credentials')
                         ->withInput()
                         ->set('hideNavbar', true)
                         ->set('hideSidebar', true);
    }

        return redirect()->to('/survey');
    }

    // Show registration form
    public function registerForm()
    {
         return view('auth/register', [
         'hideNavbar'  => true,
         'hideSidebar' => true,
    ]);
    }

    // Handle registration POST
    public function register()
{
    $post = $this->request->getPost();

    // 1) Validation
    $validation = \Config\Services::validation();
    $validation->setRules([
        'firstname'    => 'required|max_length[200]',
        'lastname'     => 'required|max_length[200]',
        'email'        => 'required|valid_email',
        'password'     => 'required|min_length[6]|matches[pass_confirm]',
        'pass_confirm' => 'required',
        // Optional fields
        'middlename'   => 'permit_empty|max_length[200]',
        'contact'      => 'permit_empty|max_length[100]',
        'address'      => 'permit_empty',
        // Your demographics (keep as before)...
        'age_group'        => 'required',
        'gender'           => 'required',
        'religion'         => 'required',
        'ethnicity'        => 'required',
        'marital_status'   => 'required',
        'children_count'   => 'required',
        'education_level'  => 'required',
        'job_band'         => 'required',
        'service_duration' => 'required',
        'salary_range'     => 'required',
        'household_income' => 'required',
    ]);

    if (! $validation->withRequest($this->request)->run()) {
        return redirect()->back()
                         ->with('errors', $validation->getErrors())
                         ->withInput();
    }

    // 2) Email uniqueness (manual because of Oracle quirks)
    $userModel = new \App\Models\UserModel();
    if ($userModel->where('EMAIL', $post['email'])->countAllResults() > 0) {
        return redirect()->back()
                         ->with('error', 'That email is already registered.')
                         ->withInput();
    }

    // 3) Build data array
    $data = [
        'FIRSTNAME'        => $post['firstname'],
        'LASTNAME'         => $post['lastname'],
        'MIDDLENAME'       => $post['middlename'] ?? null,
        'CONTACT'          => $post['contact']    ?? null,
        'ADDRESS'          => $post['address']    ?? null,
        'EMAIL'            => $post['email'],
        'PASSWORD'         => password_hash($post['password'], PASSWORD_DEFAULT),
        'TYPE'             => 2,   // 2 = normal user
        // Demographic answers
        'AGE_GROUP'        => $post['age_group'],
        'GENDER'           => $post['gender'],
        'RELIGION'         => $post['religion'],
        'ETHNICITY'        => $post['ethnicity'],
        'MARITAL_STATUS'   => $post['marital_status'],
        'CHILDREN_COUNT'   => $post['children_count'],
        'EDUCATION_LEVEL'  => $post['education_level'],
        'JOB_BAND'         => $post['job_band'],
        'SERVICE_DURATION' => $post['service_duration'],
        'SALARY_RANGE'     => $post['salary_range'],
        'HOUSEHOLD_INCOME' => $post['household_income'],
    ];
    $model->insert($data);

    // 4) Save and redirect
    $userModel->insert($data);
    return redirect()->to('/login')->with('success', 'Registration successful—please log in.');
}

    // Logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}
