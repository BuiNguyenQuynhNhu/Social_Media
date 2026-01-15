<?php

namespace App\Controllers;

use App\Models\Requirement;
use App\Libraries\MongoDB;
use Ramsey\Uuid\Uuid;

class RequireController extends BaseController
{
    protected $requireSql;
    protected $mongo;
    protected $userId;

    public function __construct() {
        $this->requireSql = new Requirement();
        $this->mongo = new MongoDB();
        $this->userId = session()->get('user_id');
    }

    public function setQuestions($groupId)
    {
        $questions = $this->request->getPost('questions');
        
        if ($this->requireSql->setConfig($groupId, $this->userId)) {
            $this->mongo->getCollection('requirement_configs')->replaceOne(
                ['_id' => $groupId],
                [
                    '_id'       => $groupId,
                    'questions' => $questions,
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                ['upsert' => true]
            );
            return $this->response->setJSON(['status' => 'success']);
        }
    }

    public function submitAnswers($groupId)
    {
        $answers = $this->request->getPost('answers');
        $submissionId = Uuid::uuid4()->toString();

        $mongoData = [
            '_id'        => $submissionId,
            'group_id'   => $groupId,
            'user_id'    => $this->userId,
            'responses'  => $answers,
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->mongo->getCollection('requirement_submissions')->insertOne($mongoData);

        return $this->response->setJSON(['status' => 'success', 'message' => 'Answers submitted!']);
    }
}