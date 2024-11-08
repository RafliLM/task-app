<?php

namespace App\Controllers;

use App\Models\TaskModel;
use CodeIgniter\HTTP\Response;

class TaskController extends BaseController
{
    public function index()
    {
        return view('task/index');
    }

    public function getTasks()
    {
        $model = new TaskModel();
        return $this->response->setJSON($model->findAll());
    }

    public function addTask()
    {
        $model = new TaskModel();
        $data = [
            'title' => $this->request->getPost('title'),
            'status' => 0
        ];
        $model->insert($data);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function updateTaskStatus($id)
    {
        $model = new TaskModel();
        $status = $this->request->getPost('status');
        $model->update($id, ['status' => $status]);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function deleteTask($id)
    {
        $model = new TaskModel();
        $model->delete($id);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function getTaskById($id)
    {
        $model = new TaskModel();
        $task = $model->find($id);
        return $this->response->setJSON($task);
    }

    public function updateTask($id)
    {
        $model = new TaskModel();
        parse_str(file_get_contents("php://input"),$sent_vars);
        if ($this->request->getMethod() === 'PUT') {
            $data = [
                'title' => $sent_vars['title'],
                'status' => $sent_vars['status']
                // 'title' => $this->request->getVar('title'),
                // 'status' => $this->request->getVar('status')
            ];
            $model->update($id, $data);
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Method Not Allowed']);
        }
    }

}
