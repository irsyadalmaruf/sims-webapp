<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{

    public function login()
    {
        return view('login/login'); 
    }

    public function process()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            session()->set('isLoggedIn', true);
            session()->set('userId', $user['id']); 
            session()->set('email', $user['email']);
            session()->set('name', $user['name']); 
            session()->set('position', $user['position']); 
            session()->set('profile_pic', $user['profile_pic']); 

            session()->setFlashdata('success', 'Login Berhasil');
            return redirect()->to('/login');
        } else {
            session()->setFlashdata('error', 'Email atau password salah');
            return redirect()->to('/login'); 
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
