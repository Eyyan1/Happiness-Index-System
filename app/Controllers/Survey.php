<?php namespace App\Controllers;

use App\Models\SurveyModel;
use CodeIgniter\Controller;

class Survey extends Controller
{
  public function index()
{
    $model = new \App\Models\SurveyModel();
    $surveys = $model->findAll();

    foreach ($surveys as &$survey) {
        // 🔧 Convert Oracle CLOB (DESCRIPTION)
        if (is_object($survey['DESCRIPTION']) && method_exists($survey['DESCRIPTION'], 'read')) {
            $survey['DESCRIPTION'] = $survey['DESCRIPTION']->read($survey['DESCRIPTION']->size());
        }

        // 🔧 Fix Oracle ID type — convert to usable integer
        if (is_object($survey['ID']) && method_exists($survey['ID'], 'read')) {
            $idValue = $survey['ID']->read($survey['ID']->size());
            $survey['ID'] = is_numeric($idValue) ? (int) $idValue : null;
        } elseif (is_object($survey['ID']) && method_exists($survey['ID'], 'load')) {
            $idValue = $survey['ID']->load();
            $survey['ID'] = is_numeric($idValue) ? (int) $idValue : null;
        } else {
            $survey['ID'] = is_numeric($survey['ID']) ? (int) $survey['ID'] : null;
        }

        // 🛡 Skip entries with invalid ID
        if ($survey['ID'] === null) {
            continue;
        }

        // 🔧 Format Oracle DATE
        if (isset($survey['START_DATE']) && is_object($survey['START_DATE'])) {
            $survey['START_DATE'] = $survey['START_DATE']->format('Y-m-d');
        }
        if (isset($survey['END_DATE']) && is_object($survey['END_DATE'])) {
            $survey['END_DATE'] = $survey['END_DATE']->format('Y-m-d');
        }
    }

    // Optional: Debug actual IDs to verify
    /*
    echo "<pre>ID Debug:\n";
    foreach ($surveys as $s) {
        var_dump($s['ID']);
    }
    echo "</pre>";
    exit;
    */

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

        if (oci_execute($stid, OCI_NO_AUTO_COMMIT)) {
            if ($descClob->save($description)) {
                oci_commit($conn);
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

        return redirect()->to('/survey')->with('message', $message);

    } catch (\Throwable $e) {
        return redirect()->back()->with('error', '❌ Exception: ' . $e->getMessage());
    }
}

  public function show($id = null)
{
    // Validate input ID
    if (!is_numeric($id) || $id < 1) {
        return redirect()->to('/survey')->with('error', 'Invalid survey ID.');
    }

    // Load models
    $surveyModel   = new \App\Models\SurveyModel();
    $questionModel = new \App\Models\QuestionModel();
    $optionModel   = new \App\Models\OptionModel(); // Model for QUESTION_OPTIONS table

    // Fetch the survey
    $survey = $surveyModel->find($id);
    if (!$survey) {
        return redirect()->to('/survey')->with('error', 'Survey not found.');
    }

    // Convert CLOB (DESCRIPTION)
    if (is_object($survey['DESCRIPTION']) && method_exists($survey['DESCRIPTION'], 'read')) {
        $survey['DESCRIPTION'] = $survey['DESCRIPTION']->read($survey['DESCRIPTION']->size());
    }

    // Format Oracle DATE fields
    if (is_object($survey['START_DATE'])) {
        $survey['START_DATE'] = $survey['START_DATE']->format('Y-m-d');
    }
    if (is_object($survey['END_DATE'])) {
        $survey['END_DATE'] = $survey['END_DATE']->format('Y-m-d');
    }

    // Fetch questions for this survey
    $questions = $questionModel
                    ->where('SURVEY_ID', $id)
                    ->orderBy('ORDER_BY', 'ASC')
                    ->findAll();

    // Process each question and its options
    foreach ($questions as &$q) {
        // Convert CLOB in QUESTION
        if (is_object($q['QUESTION']) && method_exists($q['QUESTION'], 'read')) {
            $q['QUESTION'] = $q['QUESTION']->read($q['QUESTION']->size());
        }

        $options = $optionModel
                    ->where('QUESTION_ID', $q['ID'])
                    ->orderBy('ORDER_BY', 'ASC')
                    ->findAll();

        // Convert CLOBs in OPTION_TEXT
        foreach ($options as &$opt) {
            if (is_object($opt['OPTION_TEXT']) && method_exists($opt['OPTION_TEXT'], 'read')) {
                $opt['OPTION_TEXT'] = $opt['OPTION_TEXT']->read($opt['OPTION_TEXT']->size());
            }
        }

        $q['OPTIONS'] = $options;
    }

    return view('survey/show', [
        'survey'    => $survey,
        'questions' => $questions
    ]);
}



    public function edit($id = null)
{
    // 🛠 Cast ID to integer
    $id = (int) $id;

    if ($id <= 0) {
        return redirect()->to('/survey')->with('error', 'Invalid survey ID.');
    }

    $model = new \App\Models\SurveyModel();
    $survey = $model->find($id);

    if (!$survey) {
        return redirect()->to('/survey')->with('error', 'Survey not found.');
    }

    // Convert Oracle CLOB and date if needed
    if (is_object($survey['DESCRIPTION']) && method_exists($survey['DESCRIPTION'], 'read')) {
        $survey['DESCRIPTION'] = $survey['DESCRIPTION']->read($survey['DESCRIPTION']->size());
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

    // Check logical dates
    $start = strtotime($this->request->getPost('start_date'));
    $end = strtotime($this->request->getPost('end_date'));

    if ($end < $start) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'End date cannot be earlier than start date.');
    }

    // Collect and map data
    $post = $this->request->getPost();
    $data = [];

    foreach ($post as $k => $v) {
        $data[strtoupper($k)] = $v;
    }

    // Execute raw SQL for Oracle TO_DATE handling
    $db = \Config\Database::connect();
    $sql = "
        UPDATE SURVEY_SETS SET
            TITLE = :TITLE:,
            DESCRIPTION = :DESCRIPTION:,
            START_DATE = TO_DATE(:START_DATE:, 'YYYY-MM-DD'),
            END_DATE = TO_DATE(:END_DATE:, 'YYYY-MM-DD')
        WHERE ID = :ID:
    ";

    $data['ID'] = $id; // bind ID

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
