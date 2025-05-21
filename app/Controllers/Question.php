<?php namespace App\Controllers;

use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use CodeIgniter\Controller;

class Question extends Controller
{
    public function index()
    {
        $model = new QuestionModel();
        return view('templates/header')
            . view('question/index', ['questions' => $model->findAll()])
            . view('templates/footer');
    }

    public function show($id = null)
    {
        $model = new QuestionModel();
        $data['question'] = $model->find($id);
        return view('templates/header')
            . view('question/show', $data)
            . view('templates/footer');
    }

    public function edit($id = null)
    {
        $model = new QuestionModel();
        $data['question'] = $model->find($id);
        return view('templates/header')
            . view('question/edit', $data)
            . view('templates/footer');
    }

    public function update($id = null)
    {
        $model = new QuestionModel();
        $model->update($id, $this->request->getPost());
        return redirect()->to('/question');
    }

    public function delete($id = null)
{
    // Fallback: read ID from URL manually if not passed
    if (!$id && is_numeric($this->request->uri->getSegment(3))) {
        $id = (int) $this->request->uri->getSegment(3);
    }

    if (!is_numeric($id)) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid ID']);
    }

    $model = new \App\Models\QuestionModel();
    $optionModel = new \App\Models\OptionModel();

    try {
        $optionModel->where('QUESTION_ID', $id)->delete();
        $model->delete($id);

        return $this->response->setJSON(['success' => true]);
    } catch (\Throwable $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error deleting question.',
            'error' => $e->getMessage()
        ]);
    }
}


    // AJAX save method using separate table for options
   public function create()
{
    try {
        $json = $this->request->getJSON(true);

        if (!$json || empty($json['question']) || empty($json['type']) || empty($json['survey_id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required fields.'
            ]);
        }

        $questionText = $json['question'];
        $questionType = $json['type'];
        $optionsArray = $json['options'] ?? [];
        $surveyId     = (int) $json['survey_id'];
        $order        = 1;
        $createdAt    = date('Y-m-d H:i:s');

        $conn = oci_connect('survey_db', 'survey_db_pwd', 'localhost/XEPDB1', 'AL32UTF8');
        if (!$conn) {
            $err = oci_error();
            return $this->response->setJSON(['success' => false, 'message' => '❌ Oracle DB connection failed', 'error' => $err['message']]);
        }

        // Insert question
        $sql = "INSERT INTO QUESTIONS (
                    ID, QUESTION, TYPE, ORDER_BY, SURVEY_ID, DATE_CREATED
                ) VALUES (
                    QUESTION_SEQ.NEXTVAL, :question, :type, :order_by, :survey_id,
                    TO_TIMESTAMP(:created_at, 'YYYY-MM-DD HH24:MI:SS')
                )
                RETURNING ID INTO :new_id";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':question', $questionText);
        oci_bind_by_name($stmt, ':type', $questionType);
        oci_bind_by_name($stmt, ':order_by', $order);
        oci_bind_by_name($stmt, ':survey_id', $surveyId);
        oci_bind_by_name($stmt, ':created_at', $createdAt);
        oci_bind_by_name($stmt, ':new_id', $newQuestionId, 10);

        if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
            $e = oci_error($stmt);
            oci_rollback($conn);
            oci_free_statement($stmt);
            oci_close($conn);
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Failed to insert question',
                'error'   => $e['message']
            ]);
        }

        // Insert options
        foreach ($optionsArray as $index => $optText) {
            $optStmt = oci_parse($conn, "INSERT INTO QUESTION_OPTIONS (
                ID, QUESTION_ID, OPTION_TEXT, ORDER_BY
            ) VALUES (
                QUESTION_OPTIONS_SEQ.NEXTVAL, :qid, :opt, :ord
            )");

            $orderNo = $index + 1;
            oci_bind_by_name($optStmt, ':qid', $newQuestionId);
            oci_bind_by_name($optStmt, ':opt', $optText);
            oci_bind_by_name($optStmt, ':ord', $orderNo);

            if (!oci_execute($optStmt, OCI_NO_AUTO_COMMIT)) {
                $e = oci_error($optStmt);
                oci_rollback($conn);
                oci_free_statement($optStmt);
                oci_free_statement($stmt);
                oci_close($conn);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => '❌ Failed to insert option',
                    'error'   => $e['message']
                ]);
            }

            oci_free_statement($optStmt);
        }

        oci_commit($conn);
        oci_free_statement($stmt);
        oci_close($conn);

        return $this->response->setJSON([
            'success' => true,
            'message' => '✅ Question and options saved successfully!'
        ]);

    } catch (\Throwable $e) {
        return $this->response->setJSON([
            'success' => false,
            'message' => '❌ Exception thrown',
            'error'   => $e->getMessage()
        ]);
    }
}



}
