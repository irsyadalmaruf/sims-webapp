<?php

namespace App\Controllers;

use App\Models\ProfilModel;

class Profil extends BaseController
{
    public function index()
    {
        $this->profilModel = new ProfilModel();
    
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    
        $userId = session()->get('userId');
        if (!$userId) {
            return redirect()->to('/login');
        }
    
        $user = $this->profilModel->find($userId);
    
        return view('profil/profil', ['user' => $user]);
    }

    public function update()
    {
        $this->profilModel = new ProfilModel();
    
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
    
        $userId = session()->get('userId');
        if (!$userId) {
            return redirect()->to('/login');
        }
    
        $user = $this->profilModel->find($userId);
    
        $validationRules = [
            'name' => 'required|min_length[3]',
            'position' => 'required|min_length[3]',
        ];
    
        if ($this->request->getFile('profile_pic') && $this->request->getFile('profile_pic')->isValid() && !$this->request->getFile('profile_pic')->hasMoved()) {
            $validationRules['profile_pic'] = 'uploaded[profile_pic]|max_size[profile_pic,100]|is_image[profile_pic]|mime_in[profile_pic,image/jpg,image/jpeg,image/png]';
        }
    
        $validationMessages = [
            'profile_pic' => [
                'max_size' => 'Ukuran gambar maksimal 100KB.',
                'is_image' => 'File yang diunggah bukan gambar.',
                'mime_in' => 'Format gambar harus JPG atau PNG.',
            ],
            'name' => [
                'required' => 'Nama harus diisi.',
                'min_length' => 'Nama minimal 3 karakter.',
            ],
            'position' => [
                'required' => 'Posisi harus diisi.',
                'min_length' => 'Posisi minimal 3 karakter.',
            ],
        ];
    
        if (!$this->validate($validationRules, $validationMessages)) {
            $errors = $this->validator->getErrors();
            return redirect()->back()->withInput()->with('swal', [
                'icon' => 'error',
                'title' => 'Ada Kesalahan!',
                'html' => implode('<br>', $errors),
                'timer' => 1500,
                'showConfirmButton' => false,
            ]);
        }
    
        $imageName = $user['profile_pic'];
    
        if ($this->request->getFile('profile_pic')->isValid()) {
            $profilePic = $this->request->getFile('profile_pic');
    
            $profilePicName = strtolower(str_replace(' ', '', $user['name']));
            $profilePicName = preg_replace('/[^a-z0-9]/', '', $profilePicName); 
    
            $profilePicName .= \CodeIgniter\I18n\Time::now('Asia/Jakarta', 'en_ID')->format('dmyHis') . '.' . $profilePic->getExtension();
    
            $profilePic->move(ROOTPATH . 'public/Assets/img/profil', $profilePicName);
    
            if ($user['profile_pic'] && file_exists(ROOTPATH . 'public/Assets/img/profil/' . $user['profile_pic'])) {
                unlink(ROOTPATH . 'public/Assets/img/profil/' . $user['profile_pic']);
            }
    
            $imageName = $profilePicName;
        }
    
        $this->profilModel->update($userId, [
            'name' => $this->request->getPost('name'),
            'position' => $this->request->getPost('position'),
            'profile_pic' => $imageName,
        ]);
    
        return redirect()->to('/profil')->with('swal', [
            'icon' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data berhasil diubah.',
        ]);
    }    
      
}