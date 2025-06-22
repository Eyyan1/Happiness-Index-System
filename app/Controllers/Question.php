<?php namespace App\Controllers;

use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use CodeIgniter\Controller;

class Question extends Controller
{
    public function index()
    {
        $model = new QuestionModel();
        $questions = $model->findAll();

        // Convert CLOB if needed
        foreach ($questions as &$q) {
            if (is_object($q['QUESTION']) && method_exists($q['QUESTION'], 'read')) {
                $len = $q['QUESTION']->size();
                $q['QUESTION'] = $len > 0 ? $q['QUESTION']->read($len) : '';
            }
        }

        // Debug (optional)
        // echo '<pre>'; print_r($questions); echo '</pre>'; exit;

        return view('templates/header')
            . view('question/index', ['questions' => $questions])
            . view('templates/footer');
    }

    public function show($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/question')->with('error', 'Invalid question ID.');
        }

        $model = new QuestionModel();
        $question = $model->find((int)$id);

        // Convert CLOB
        if (is_object($question['QUESTION']) && method_exists($question['QUESTION'], 'read')) {
            $len = $question['QUESTION']->size();
            $question['QUESTION'] = $len > 0 ? $question['QUESTION']->read($len) : '';
        }

        return view('templates/header')
            . view('question/show', ['question' => $question])
            . view('templates/footer');
    }

    public function edit($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/question')->with('error', 'Invalid question ID.');
        }

        $model = new QuestionModel();
        $data['question'] = $model->find((int)$id);

        return view('templates/header')
            . view('question/edit', $data)
            . view('templates/footer');
    }

    public function update($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/question')->with('error', 'Invalid ID.');
        }

        $model = new QuestionModel();
        $model->update((int)$id, $this->request->getPost());

        return redirect()->to('/question');
    }

    public function remove($id = null)
    {
        if (!is_numeric($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid ID']);
        }

        $questionModel = new QuestionModel();
        $optionModel = new QuestionOptionModel();

        try {
            $optionModel->where('QUESTION_ID', (int)$id)->delete();
            $questionModel->delete((int)$id);

            return $this->response->setJSON(['success' => true, 'message' => '✅ Deleted']);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Deletion failed.',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function create()
    {
        try {
            $json = $this->request->getJSON(true);

            if (!$json || empty($json['survey_id']) || empty($json['section_id']) || empty($json['question']) || empty($json['type'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required fields.'
                ]);
            }

            $surveyId  = (int)$json['survey_id'];
            $sectionId = (int)$json['section_id'];
            $question  = $json['question'];
            $type      = $json['type'];
            $options   = $json['options'] ?? [];
            $order     = 1;
            $createdAt = date('Y-m-d H:i:s');

            $conn = oci_connect('pita207', 'pita207', 'localhost/XEPDB1', 'AL32UTF8');
            if (!$conn) {
                $err = oci_error();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => '❌ Oracle DB connection failed',
                    'error'   => $err['message']
                ]);
            }

            $sql = "INSERT INTO QUESTIONS (
                        ID, SECTION_ID, SURVEY_ID, QUESTION, TYPE, ORDER_BY, DATE_CREATED
                    ) VALUES (
                        QUESTION_SEQ.NEXTVAL, :section_id, :survey_id, :question, :type, :order_by,
                        TO_TIMESTAMP(:created_at, 'YYYY-MM-DD HH24:MI:SS')
                    )
                    RETURNING ID INTO :new_id";

            $stmt = oci_parse($conn, $sql);
            oci_bind_by_name($stmt, ':section_id', $sectionId);
            oci_bind_by_name($stmt, ':survey_id', $surveyId);
            oci_bind_by_name($stmt, ':question', $question);
            oci_bind_by_name($stmt, ':type', $type);
            oci_bind_by_name($stmt, ':order_by', $order);
            oci_bind_by_name($stmt, ':created_at', $createdAt);
            oci_bind_by_name($stmt, ':new_id', $newQuestionId, 10);

            if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
                $e = oci_error($stmt);
                oci_rollback($conn);
                oci_free_statement($stmt);
                oci_close($conn);

                return $this->response->setJSON([
                    'success' => false,
                    'message' => '❌ Failed to insert question.',
                    'error'   => $e['message']
                ]);
            }

            // Now insert OPTIONS:
            foreach ($options as $index => $optText) {
                $optStmt = oci_parse($conn, "INSERT INTO QUESTION_OPTIONS (
                    ID, QUESTION_ID, OPTION_TEXT, ORDER_BY
                ) VALUES (
                    QUESTION_OPTIONS_SEQ.NEXTVAL, :qid, :opt_text, :order_no
                )");

                $orderNo = $index + 1;
                oci_bind_by_name($optStmt, ':qid', $newQuestionId);
                oci_bind_by_name($optStmt, ':opt_text', $optText);
                oci_bind_by_name($optStmt, ':order_no', $orderNo);

                if (!oci_execute($optStmt, OCI_NO_AUTO_COMMIT)) {
                    $e = oci_error($optStmt);
                    oci_rollback($conn);
                    oci_free_statement($optStmt);
                    oci_free_statement($stmt);
                    oci_close($conn);

                    return $this->response->setJSON([
                        'success' => false,
                        'message' => '❌ Failed to insert option.',
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
