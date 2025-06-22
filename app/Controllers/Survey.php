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
        $title       = $this->request->getPost('title');
        $startDate   = $this->request->getPost('start_date');
        $endDate     = $this->request->getPost('end_date');
        $description = $this->request->getPost('description');

        if (!$title || !$startDate || !$endDate) {
            return redirect()->back()->with('error', '❌ Missing required fields');
        }

        // Direct oci_connect
        $conn = oci_connect('pita207', 'pita207', '//localhost:49161/xepdb1', 'AL32UTF8');
        if (!$conn) {
            $err = oci_error();
            return redirect()->back()->with('error', '❌ Oracle connection failed: ' . $err['message']);
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
        if (oci_execute($stid, OCI_NO_AUTO_COMMIT)) {
            if ($descClob->save($description)) {
                oci_commit($conn);
                $success = true;
            } else {
                oci_rollback($conn);
            }
        } else {
            oci_rollback($conn);
        }

        $descClob->free();
        oci_free_statement($stid);
        oci_close($conn);

        if ($success) {
            return redirect()->to('/survey')->with('success', '✅ Survey created successfully.');
        } else {
            return redirect()->back()->with('error', '❌ Failed to create survey.');
        }

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

        echo view('templates/header');
        echo view('survey/edit', ['survey' => $survey]);
        echo view('templates/footer');
    }

   public function update($id = null)
{
    if (!is_numeric($id) || $id < 1) {
        return redirect()->to('/survey')->with('error', 'Invalid survey ID.');
    }

    $title       = $this->request->getPost('title');
    $startDate   = $this->request->getPost('start_date');
    $endDate     = $this->request->getPost('end_date');
    $description = $this->request->getPost('description');

    if (!$title || !$startDate || !$endDate) {
        return redirect()->back()->with('error', '❌ Missing required fields');
    }

    $db = \Config\Database::connect();

    // Handling CLOB for the description field
    $description = $db->escapeString($description);

    $updateData = [
        'TITLE'       => $title,
        'DESCRIPTION' => $description,  // Handle CLOB as string
        'START_DATE'  => date('Y-m-d', strtotime($startDate)),
        'END_DATE'    => date('Y-m-d', strtotime($endDate))
    ];

    $db->table('SURVEY_SETS')->where('ID', $id)->update($updateData);

    return redirect()->to('/survey')->with('success', 'Survey updated successfully.');
}

    public function delete($id = null)
    {
        if (!is_numeric($id)) {
            return redirect()->to('/survey')->with('error', 'Invalid survey ID.');
        }

        $db = \Config\Database::connect();

        $db->table('SURVEY_SETS')->where('ID', $id)->delete();

        return redirect()->to('/survey')->with('success', 'Survey deleted successfully.');
    }

    public function addSection($surveyId)
{
    try {
        // Get the section details from the form input
        $sectionName = $this->request->getPost('section_name');
        $sectionDescription = $this->request->getPost('section_description');
        
        if (!$sectionName) {
            return redirect()->back()->with('error', '❌ Section name is required');
        }

        $sectionModel = new SectionModel();

        // Add the section to the SECTIONS table
        $sectionModel->save([
            'survey_id' => $surveyId,
            'name' => $sectionName,
            'description' => $sectionDescription, // Optional field
            'order_by' => 1 // You can dynamically handle the order
        ]);

        return redirect()->to('/survey/' . $surveyId)->with('success', '✅ Section added successfully');
    } catch (\Throwable $e) {
        return redirect()->back()->with('error', '❌ Exception: ' . $e->getMessage());
    }
}

}
