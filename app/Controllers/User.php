<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UserModel;

class User extends Controller
{
    public function index()
    {
        $model          = new UserModel();
        $data['users']  = $model->findAll();

        echo view('templates/header');
        echo view('user/index', $data);
        echo view('templates/footer');
    }

    public function new()
    {
        echo view('templates/header');
        echo view('user/new');
        echo view('templates/footer');
    }

    public function create()
    {
        $post = $this->request->getPost();
        if (empty($post)) {
            return redirect()->back()->with('error', 'No data submitted.');
        }

        // Uppercase keys to match Oracle column names
        $data = [];
        foreach ($post as $k => $v) {
            $data[strtoupper($k)] = $v;
        }

        // Hash password
        if (isset($data['PASSWORD'])) {
            $data['PASSWORD'] = password_hash($data['PASSWORD'], PASSWORD_DEFAULT);
        }

        (new UserModel())->insert($data);

        return redirect()->to('/user')->with('success', 'User created.');
    }

    public function show($id = null)
    {
        $model         = new UserModel();
        $data['user']  = $model->find($id);

        echo view('templates/header');
        echo view('user/show', $data);
        echo view('templates/footer');
    }

    public function edit($id = null)
    {
        $model         = new UserModel();
        $data['user']  = $model->find($id);

        echo view('templates/header');
        echo view('user/edit', $data);
        echo view('templates/footer');
    }

    public function update($id = null)
    {
        $post = $this->request->getPost();
        if (empty($post)) {
            return redirect()->back()->with('error', 'No data submitted.');
        }

        $data = [];
        foreach ($post as $k => $v) {
            // Skip empty password fields so we don’t overwrite existing
            if ($k === 'password' && empty($v)) {
                continue;
            }

            if ($k === 'password') {
                continue;
            }

            $data[strtoupper($k)] = $v;
        }

        (new UserModel())->update($id, $data);

        return redirect()->to('/user')->with('success', 'User updated.');
    }

    public function delete($id = null)
    {
        (new UserModel())->delete($id);
        return redirect()->to('/user')->with('success', 'User deleted.');
    }
}
