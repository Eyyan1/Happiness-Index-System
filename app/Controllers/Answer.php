<?php namespace App\Controllers;
use App\Models\AnswerModel;
use CodeIgniter\Controller;

class Answer extends Controller
{
    public function index()
    {
        $model = new AnswerModel();
        echo view('templates/header');
        echo view('answer/index', ['answers' => $model->findAll()]);
        echo view('templates/footer');
    }
    public function new()
    {
        echo view('templates/header');
        echo view('answer/new');
        echo view('templates/footer');
    }
    public function create()
    {
        (new AnswerModel())->save($this->request->getPost());
        return redirect()->to('/answer');
    }
    public function show($id = null)
    {
        $data['answer'] = (new AnswerModel())->find($id);
        echo view('templates/header');
        echo view('answer/show', $data);
        echo view('templates/footer');
    }
    public function edit($id = null)
    {
        $data['answer'] = (new AnswerModel())->find($id);
        echo view('templates/header');
        echo view('answer/edit', $data);
        echo view('templates/footer');
    }
    public function update($id = null)
    {
        (new AnswerModel())->update($id, $this->request->getPost());
        return redirect()->to('/answer');
    }
    public function delete($id = null)
    {
        (new AnswerModel())->delete($id);
        return redirect()->to('/answer');
    }
}
