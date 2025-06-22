<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;
use App\Models\AnswerModel;
use App\Models\NotificationModel;

class User extends Controller
{
    protected $userModel;
    protected $ansModel;
    protected $notifModel;

    public function __construct()
    {
        helper(['form', 'url']);

        $this->userModel  = new UserModel();
        $this->ansModel   = new AnswerModel();
        $this->notifModel = new NotificationModel();
    }

    // ─────────────────────────────────────────────────────────────────
    // 1) Admin: List users and show recent notifications
    // ─────────────────────────────────────────────────────────────────
    public function index()
    {
        // Only real users (TYPE = 2), newest first
        $users = $this->userModel
                      ->where('TYPE', 2)
                      ->orderBy('DATE_CREATED', 'DESC')
                      ->findAll();

        foreach ($users as &$u) {
            // Has answered?
            $u['has_answered'] = $this->ansModel
                                     ->where('USER_ID', $u['ID'])
                                     ->countAllResults() > 0;

            // Last notification for this user
            $last = $this->notifModel
                         ->where('USER_ID', $u['ID'])
                         ->orderBy('DATE_CREATED', 'DESC')
                         ->first();
            $u['last_notify'] = $last['MESSAGE'] ?? '';
        }

        // Five most recent notifications site-wide
        $recentNotifs = $this->notifModel
                             ->orderBy('DATE_CREATED', 'DESC')
                             ->limit(5)
                             ->findAll();

        return view('user/index', [
            'users'         => $users,
            'notifications' => $recentNotifs,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // 2) Admin: Create new user (reuses register form)
    // ─────────────────────────────────────────────────────────────────
    public function create()
    {
        helper('validation');
        $post = $this->request->getPost();

        // Validate inputs exactly as in Auth::register()
        $rules = [
            'firstname'      => 'required|max_length[200]',
            'lastname'       => 'required|max_length[200]',
            'email'          => 'required|valid_email|is_unique[USERSs.EMAIL]',
            'password'       => 'required|min_length[6]|matches[pass_confirm]',
            'pass_confirm'   => 'required',
            // plus demographic rules...
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                             ->with('errors', $this->validator->getErrors())
                             ->withInput();
        }

        // Insert user
        $this->userModel->insert([
            'FIRSTNAME'      => $post['firstname'],
            'LASTNAME'       => $post['lastname'],
            'EMAIL'          => $post['email'],
            'PASSWORD'       => password_hash($post['password'], PASSWORD_DEFAULT),
            'TYPE'           => 2, // standard user
            'DATE_CREATED'   => date('Y-m-d H:i:s'),
            // demographics...
        ]);

        return redirect()->to('/user')
                         ->with('success', 'New user added.');
    }

    // ─────────────────────────────────────────────────────────────────
    // 3) Admin: Update user (via AJAX-loaded form)
    // ─────────────────────────────────────────────────────────────────
    // In app/Controllers/User.php

        public function edit($id = null)
{
    $user = $this->userModel->find($id);

    // If ADDRESS came back as an OCILob, read it out:
    if (isset($user['ADDRESS']) 
        && is_object($user['ADDRESS']) 
        && method_exists($user['ADDRESS'], 'read'))
    {
        $length = $user['ADDRESS']->size();
        $user['ADDRESS'] = $length
            ? $user['ADDRESS']->read($length)
            : '';
    }

    return view('user/edit', [
        'hideSidebar' => true,
        'hideNavbar'  => false,
        'user'        => $user,
    ]);
}


    public function update($id = null)
    {
        $post = $this->request->getPost();
        $rules = [
            'firstname'    => 'required|max_length[200]',
            'lastname'     => 'required|max_length[200]',
            'email'        => "required|valid_email|is_unique[USERSs.EMAIL,ID,{$id}]",
            // optional password change:
        ];
        if (!empty($post['password'])) {
            $rules['password']     = 'min_length[6]|matches[pass_confirm]';
            $rules['pass_confirm'] = 'required';
        }
        if (! $this->validate($rules)) {
            return redirect()->back()
                             ->with('errors', $this->validator->getErrors())
                             ->withInput();
        }

        $data = [
            'FIRSTNAME' => $post['firstname'],
            'LASTNAME'  => $post['lastname'],
            'EMAIL'     => $post['email'],
        ];
        if (!empty($post['password'])) {
            $data['PASSWORD'] = password_hash($post['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update((int)$id, $data);

        return redirect()->to('/user')
                         ->with('success', 'User updated.');
    }

    // ─────────────────────────────────────────────────────────────────
    // 4) Admin: Delete a user (AJAX)
    // ─────────────────────────────────────────────────────────────────
    public function delete($id = null)
    {
        $ok = $this->userModel->delete((int)$id);
        return $this->response->setJSON(['success' => (bool)$ok]);
    }

    // ─────────────────────────────────────────────────────────────────
    // 5) Admin: Send notification (AJAX)
    // ─────────────────────────────────────────────────────────────────
    public function notify($id = null)
    {
        $post = $this->request->getPost();
        $msg  = $post['message'] ?? 'Please complete your survey';

        $this->notifModel->insert([
            'USER_ID'      => (int)$id,
            'MESSAGE'      => $msg,
            'IS_READ'      => 'N',
            'DATE_CREATED' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────────
    // 6) User: View own profile
    // ─────────────────────────────────────────────────────────────────
    public function profile()
    {
        $userId = session('userId');
        $user   = $this->userModel->find($userId);

        // Convert CLOB if needed...
        if (is_object($user['ADDRESS']) && method_exists($user['ADDRESS'], 'read')) {
            $len = $user['ADDRESS']->size();
            $user['ADDRESS'] = $len ? $user['ADDRESS']->read($len) : '';
        }

        return view('user/profile', [
            'hideSidebar' => true,
            'hideNavbar'  => false,
            'user'        => $user,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    // 7) User: Update own profile
    // ─────────────────────────────────────────────────────────────────
    public function updateProfile()
    {
        helper('validation');
        $userId = session('userId');
        $post   = $this->request->getPost();

        $rules = [
            'firstname'    => 'required|max_length[200]',
            'lastname'     => 'required|max_length[200]',
            'contact'      => 'permit_empty|max_length[100]',
            'address'      => 'permit_empty',
        ];
        if (!empty($post['password'])) {
            $rules['password']     = 'min_length[6]|matches[pass_confirm]';
            $rules['pass_confirm'] = 'required';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()
                             ->with('errors', $this->validator->getErrors())
                             ->withInput();
        }

        $data = [
            'FIRSTNAME' => $post['firstname'],
            'LASTNAME'  => $post['lastname'],
            'CONTACT'   => $post['contact'] ?: null,
            'ADDRESS'   => $post['address'] ?: null,
        ];
        if (!empty($post['password'])) {
            $data['PASSWORD'] = password_hash($post['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $data);
        session()->set('name', $post['firstname'].' '.$post['lastname']);

        return redirect()->to('/profile')
                         ->with('success', 'Profile updated successfully.');
    }

    public function show($id = null)
    {
        $user = $this->userModel->find($id);

        if (! $user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("User not found: $id");
        }

        // Convert CLOB address if needed
        if (is_object($user['ADDRESS']) && method_exists($user['ADDRESS'], 'read')) {
            $user['ADDRESS'] = $user['ADDRESS']->read($user['ADDRESS']->size());
        }

        return view('user/show', [
            'user'        => $user,
            'hideSidebar' => false,
            'hideNavbar'  => false,
        ]);
    }
}

