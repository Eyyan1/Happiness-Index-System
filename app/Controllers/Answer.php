<?php namespace App\Controllers;

use App\Models\SurveyModel;
use App\Models\SectionModel;
use App\Models\QuestionModel;
use App\Models\OptionModel;
use App\Models\AnswerModel;
use CodeIgniter\Controller;

class Answer extends Controller
{
    public function index()
    {
        $model   = new AnswerModel();
        $answers = $model->orderBy('DATE_CREATED', 'DESC')->findAll();

        // If any ANSWER_TEXT is a CLOB, convert to string
        foreach ($answers as &$a) {
            if (is_object($a['ANSWER_TEXT']) && method_exists($a['ANSWER_TEXT'], 'read')) {
                $len = $a['ANSWER_TEXT']->size();
                $a['ANSWER_TEXT'] = $len ? $a['ANSWER_TEXT']->read($len) : '';
            }
        }

        return view('answer/index', ['answers' => $answers]);
    }

    public function takeSurvey($surveyId = null)
    {
        if (! is_numeric($surveyId)) {
            return redirect()->to('/survey')->with('error','Invalid survey ID.');
        }

        $surveyModel   = new SurveyModel();
        $sectionModel  = new SectionModel();
        $questionModel = new QuestionModel();
        $optionModel   = new OptionModel();

        $survey = $surveyModel->find((int)$surveyId);
        if (! $survey) {
            return redirect()->to('/survey')->with('error','Survey not found.');
        }

        // Decode CLOB description
        if (is_object($survey['DESCRIPTION']) && method_exists($survey['DESCRIPTION'], 'read')) {
            $len = $survey['DESCRIPTION']->size();
            $survey['DESCRIPTION'] = $len ? $survey['DESCRIPTION']->read($len) : '';
        }

        // Load sections → questions → options
        $sections = $sectionModel
            ->where('SURVEY_ID',$surveyId)
            ->orderBy('ORDER_BY')
            ->findAll();

        foreach ($sections as &$section) {
            if (is_object($section['DESCRIPTION']) && method_exists($section['DESCRIPTION'],'read')) {
                $len = $section['DESCRIPTION']->size();
                $section['DESCRIPTION'] = $len ? $section['DESCRIPTION']->read($len) : '';
            }

            $questions = $questionModel
                ->where('SECTION_ID',$section['ID'])
                ->orderBy('ORDER_BY','ASC')
                ->findAll();

            foreach ($questions as &$q) {
                if (is_object($q['QUESTION']) && method_exists($q['QUESTION'],'read')) {
                    $len = $q['QUESTION']->size();
                    $q['QUESTION'] = $len ? $q['QUESTION']->read($len) : '';
                }
                $opts = $optionModel
                    ->where('QUESTION_ID',$q['ID'])
                    ->orderBy('ORDER_BY','ASC')
                    ->findAll();
                foreach ($opts as &$opt) {
                    if (is_object($opt['OPTION_TEXT']) && method_exists($opt['OPTION_TEXT'],'read')) {
                        $len = $opt['OPTION_TEXT']->size();
                        $opt['OPTION_TEXT'] = $len ? $opt['OPTION_TEXT']->read($len) : '';
                    }
                }
                $q['OPTIONS'] = $opts;
            }
            $section['QUESTIONS'] = $questions;
        }

        $survey['SECTIONS'] = $sections;

        return view('answer/take', ['survey' => $survey]);
    }

     public function saveAnswer($surveyId = null)
    {
        if (! is_numeric($surveyId)) {
            return redirect()->to('/survey')->with('error','Invalid survey ID.');
        }

        $answers = $this->request->getPost('answers') ?? [];
        if (empty($answers)) {
            return redirect()->back()->with('error','Please answer at least one question.');
        }

        $model = new AnswerModel();
        foreach ($answers as $questionId => $value) {
    $answerData = [
        'SURVEY_ID'    => $surveyId,
        'QUESTION_ID'  => $questionId,
        'USER_ID'      => session('userId'),         // ← Add this line
        'ANSWER_TEXT'  => is_array($value) ? implode(',', $value) : $value,
        'DATE_CREATED' => date('Y-m-d H:i:s')
    ];

    $answerModel->insert($answerData);
}

        return redirect()->to('/survey')->with('success','Survey submitted successfully!');
    }
}