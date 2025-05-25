<?php namespace App\Controllers;

use App\Models\SurveyModel;
use App\Models\SectionModel;
use App\Models\QuestionModel;
use App\Models\OptionModel;
use CodeIgniter\Controller;

class Survey extends Controller
{
    public function index()
    {
        $model = new SurveyModel();
        $surveys = $model->findAll();

        foreach ($surveys as &$survey) {
            if (is_object($survey['DESCRIPTION']) && method_exists($survey['DESCRIPTION'], 'read')) {
                $len = $survey['DESCRIPTION']->size();
                $survey['DESCRIPTION'] = $len > 0 ? $survey['DESCRIPTION']->read($len) : '';
            }

            $survey['ID'] = is_numeric($survey['ID']) ? (int)$survey['ID'] : null;
            if ($survey['ID'] === null) continue;

            if (is_object($survey['START_DATE'])) {
                $survey['START_DATE'] = $survey['START_DATE']->format('Y-m-d');
            }
            if (is_object($survey['END_DATE'])) {
                $survey['END_DATE'] = $survey['END_DATE']->format('Y-m-d');
            }

            $sectionModel = new SectionModel();
            $questionModel = new QuestionModel();
            $optionModel = new OptionModel();

            $sections = $sectionModel->where('SURVEY_ID', $survey['ID'])->orderBy('ORDER_BY')->findAll();
            foreach ($sections as &$section) {
                if (is_object($section['DESCRIPTION']) && method_exists($section['DESCRIPTION'], 'read')) {
                    $len = $section['DESCRIPTION']->size();
                    $section['DESCRIPTION'] = $len > 0 ? $section['DESCRIPTION']->read($len) : '';
                }

                $section['QUESTIONS'] = $questionModel
                    ->where('SECTION_ID', $section['ID'])
                    ->orderBy('ORDER_BY', 'ASC')
                    ->findAll();

                foreach ($section['QUESTIONS'] as &$q) {
                    if (is_object($q['QUESTION']) && method_exists($q['QUESTION'], 'read')) {
                        $len = $q['QUESTION']->size();
                        $q['QUESTION'] = $len > 0 ? $q['QUESTION']->read($len) : '';
                    }

                    $q['OPTIONS'] = $optionModel
                        ->where('QUESTION_ID', $q['ID'])
                        ->orderBy('ORDER_BY', 'ASC')
                        ->findAll();

                    foreach ($q['OPTIONS'] as &$opt) {
                        if (is_object($opt['OPTION_TEXT']) && method_exists($opt['OPTION_TEXT'], 'read')) {
                            $len = $opt['OPTION_TEXT']->size();
                            $opt['OPTION_TEXT'] = $len > 0 ? $opt['OPTION_TEXT']->read($len) : '';
                        }
                    }
                }
            }

            $survey['SECTIONS'] = $sections;
        }

        echo view('templates/header');
        echo view('survey/index', ['surveys' => $surveys]);
        echo view('templates/footer');
    }

    public function new()
    {
        echo view('templates/header');
        echo view('survey/new');
        echo view('templates/footer');
    }

    public function create()
    {
        try {
            $conn = oci_connect('survey_db', 'survey_db_pwd', 'localhost/XEPDB1', 'AL32UTF8');
            if (!$conn) {
                $err = oci_error();
                return redirect()->back()->with('error', '❌ Connection failed: ' . $err['message']);
            }

            $title       = $this->request->getPost('title');
            $startDate   = $this->request->getPost('start_date');
            $endDate     = $this->request->getPost('end_date');
            $description = $this->request->getPost('description');

            if (!$title || !$startDate || !$endDate) {
                return redirect()->back()->with('error', '❌ Missing required fields');
            }

            $sql = "INSERT INTO SURVEY_SETS (
                        ID, TITLE, DESCRIPTION, START_DATE, END_DATE, DATE_CREATED
                    ) VALUES (
                        SURVEY_SEQ.NEXTVAL, :title, EMPTY_CLOB(), TO_DATE(:start_date, 'YYYY-MM-DD'),
                        TO_DATE(:end_date, 'YYYY-MM-DD'), SYSTIMESTAMP
                    )
                    RETURNING DESCRIPTION INTO :desc_clob";

            $stid = oci_parse($conn, $sql);
            oci_bind_by_name($stid, ':title', $title);
            oci_bind_by_name($stid, ':start_date', $startDate);
            oci_bind_by_name($stid, ':end_date', $endDate);

            $descClob = oci_new_descriptor($conn, OCI_D_LOB);
            oci_bind_by_name($stid, ':desc_clob', $descClob, -1, OCI_B_CLOB);

            $success = false;
            $message = '';

            if (oci_execute($stid, OCI_NO_AUTO_COMMIT)) {
                if ($descClob->save($description)) {
                    oci_commit($conn);
                    $success = true;
                    $message = '✅ Survey saved successfully.';
                } else {
                    oci_rollback($conn);
                    $message = '❌ Failed to write CLOB.';
                }
            } else {
                $e = oci_error($stid);
                oci_rollback($conn);
                $message = '❌ Insert failed: ' . $e['message'];
            }

            $descClob->free();
            oci_free_statement($stid);
            oci_close($conn);

            return redirect()->to('/survey')->with($success ? 'message' : 'error', $message);

        } catch (\Throwable $e) {
            return redirect()->back()->with('error', '❌ Exception: ' . $e->getMessage());
        }
    }

    public function show($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/survey')->with('error', 'Invalid survey ID.');
        }

        $surveyModel   = new SurveyModel();
        $sectionModel  = new SectionModel();
        $questionModel = new QuestionModel();
        $optionModel   = new OptionModel();

        $survey = $surveyModel->find((int)$id);
        if (!$survey) {
            return redirect()->to('/survey')->with('error', 'Survey not found.');
        }

        if (is_object($survey['DESCRIPTION']) && method_exists($survey['DESCRIPTION'], 'read')) {
            $len = $survey['DESCRIPTION']->size();
            $survey['DESCRIPTION'] = $len > 0 ? $survey['DESCRIPTION']->read($len) : '';
        }
        if (is_object($survey['START_DATE'])) {
            $survey['START_DATE'] = $survey['START_DATE']->format('Y-m-d');
        }
        if (is_object($survey['END_DATE'])) {
            $survey['END_DATE'] = $survey['END_DATE']->format('Y-m-d');
        }

        $sections = $sectionModel->where('SURVEY_ID', $id)->orderBy('ORDER_BY')->findAll();

        foreach ($sections as &$section) {
            if (is_object($section['DESCRIPTION']) && method_exists($section['DESCRIPTION'], 'read')) {
                $len = $section['DESCRIPTION']->size();
                $section['DESCRIPTION'] = $len > 0 ? $section['DESCRIPTION']->read($len) : '';
            }

            $questions = $questionModel
                ->where('SECTION_ID', $section['ID'])
                ->orderBy('ORDER_BY', 'ASC')
                ->findAll();

            foreach ($questions as &$q) {
                if (is_object($q['QUESTION']) && method_exists($q['QUESTION'], 'read')) {
                    $len = $q['QUESTION']->size();
                    $q['QUESTION'] = $len > 0 ? $q['QUESTION']->read($len) : '';
                }

                $options = $optionModel
                    ->where('QUESTION_ID', $q['ID'])
                    ->orderBy('ORDER_BY', 'ASC')
                    ->findAll();

                foreach ($options as &$opt) {
                    if (is_object($opt['OPTION_TEXT']) && method_exists($opt['OPTION_TEXT'], 'read')) {
                        $len = $opt['OPTION_TEXT']->size();
                        $opt['OPTION_TEXT'] = $len > 0 ? $opt['OPTION_TEXT']->read($len) : '';
                    }
                }

                $q['OPTIONS'] = $options;
            }

            $section['QUESTIONS'] = $questions;
        }

        $survey['SECTIONS'] = $sections;

        return view('survey/show', [
            'survey' => $survey
        ]);
    }

    public function edit($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->to('/survey')->with('error', 'Invalid survey ID.');
        }

        $model = new SurveyModel();
        $survey = $model->find($id);

        if (!$survey) {
            return redirect()->to('/survey')->with('error', 'Survey not found.');
        }

        if (is_object($survey['DESCRIPTION']) && method_exists($survey['DESCRIPTION'], 'read')) {
            $len = $survey['DESCRIPTION']->size();
            $survey['DESCRIPTION'] = $len > 0 ? $survey['DESCRIPTION']->read($len) : '';
        }
        if (isset($survey['START_DATE']) && is_object($survey['START_DATE'])) {
            $survey['START_DATE'] = $survey['START_DATE']->format('Y-m-d');
        }
        if (isset($survey['END_DATE']) && is_object($survey['END_DATE'])) {
            $survey['END_DATE'] = $survey['END_DATE']->format('Y-m-d');
        }

        echo view('templates/header');
        echo view('survey/edit', ['survey' => $survey]);
        echo view('templates/footer');
    }

    public function update($id = null)
    {
        if (!is_numeric($id) || $id < 1) {
            return redirect()->to('/survey')->with('error', 'Invalid survey ID.');
        }

        $rules = [
            'title'      => 'required|min_length[3]',
            'start_date' => 'required|valid_date[Y-m-d]',
            'end_date'   => 'required|valid_date[Y-m-d]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Please fix the errors below.')
                ->with('validation', $this->validator);
        }

        $start = strtotime($this->request->getPost('start_date'));
        $end = strtotime($this->request->getPost('end_date'));

        if ($end < $start) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'End date cannot be earlier than start date.');
        }

        $post = $this->request->getPost();
        $data = [];

        foreach ($post as $k => $v) {
            $data[strtoupper($k)] = $v;
        }

        $db = \Config\Database::connect();
        $sql = "
            UPDATE SURVEY_SETS SET
                TITLE = :TITLE:,
                DESCRIPTION = :DESCRIPTION:,
                START_DATE = TO_DATE(:START_DATE:, 'YYYY-MM-DD'),
                END_DATE = TO_DATE(:END_DATE:, 'YYYY-MM-DD')
            WHERE ID = :ID:
        ";

        $data['ID'] = $id;

        $db->query($sql, $data);

        return redirect()->to('/survey')->with('success', 'Survey updated successfully.');
    }

    public function delete($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/survey')->with('error', 'Invalid survey ID.');
        }

        $model = new SurveyModel();
        $model->delete($id);

        return redirect()->to('/survey')->with('success', 'Survey deleted.');
    }
}
