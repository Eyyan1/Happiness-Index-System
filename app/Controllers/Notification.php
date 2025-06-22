<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\NotificationModel;

class Notification extends Controller
{
    
    public function markAllRead()
    {
        $notifModel = new NotificationModel();
        $notifModel
            ->where('USER_ID', session('userId'))
            ->where('IS_READ','N')
            ->set(['IS_READ'=>'Y'])
            ->update();
        return $this->response->setJSON(['success'=>true]);
    }

     // 7.1: List all notifications
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $notifModel = new NotificationModel();
        $all = $notifModel
            ->where('USER_ID', session()->get('userId'))
            ->orderBy('DATE_CREATED', 'DESC')
            ->findAll();

        return view('notifications/index', [
            'notifications' => $all
        ]);
    }
}
