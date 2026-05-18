<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\ContactsModel;
use CodeIgniter\Controller;

class Restore extends Controller {
    public function __construct() {
        helper(['form', 'url', 'cookie', 'text']);
        session();
    }

    public function index($token = null) {
        if (session()->get('logged_in') && session()->get('group') == 1) {
            return redirect()->to('/games');
        } else if (session()->get('logged_in') && session()->get('group') == 0) {
            return redirect()->to('/play');
        }

        if ($token) {
            $modelUsers = new UsersModel();
            $user = $modelUsers->where('restore_token', $token)->first();

            if (!$user) {
                return redirect()->to('/restore');
            }
        }

        $modelContacts = new ContactsModel();

        $contacts = $modelContacts->findAll();
    
        $data = [
            'page' => [
                'title' => translate('forgot your password?')
            ],
            'validation' => \Config\Services::validation(),
            'contentPage' => view('restore/index', ['contacts' => $contacts, 'token' => $token])
        ];
    
        if ($this->request->isAJAX()) {
            return $this->response->setBody($data['contentPage']);
        } else {
            return view('layout/index', $data);
        }
    }

    public function restoreSubmit() {
        $modelUsers = new UsersModel();
        
        $validationRules = [
            'email' => [
                'label' => translate('email'),
                'rules' => 'required|valid_email',
            ]
        ];
        
        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            $response = [
                'success' => false,
                'errors' => $errors
            ];
            return $this->response->setJSON($response);
        }

        $email = $this->request->getPost('email');

        return $this->checkEmailExists($email);
    }

    public function checkEmailExists($email) {
        $modelUsers = new UsersModel();
        
        $user = $modelUsers->where('email', $email)->first();

        if (!$user) {
            $response = [
                'success' => false,
                'errors' => [
                    'email' => translate('the email does not belong to an account')
                ]
            ];
            return $this->response->setJSON($response);
        }

        if ($user['deleted'] == 1) {
            $response = [
                'success' => false,
                'errors' => [
                    'email' => translate('your account has been deleted')
                ]
            ];
            return $this->response->setJSON($response);
        }

        if ($user['status'] == 2) {
            $response = [
                'success' => false,
                'errors' => [
                    'email' => translate('your account is inactive')
                ]
            ];
            return $this->response->setJSON($response);
        }

        $restoreCode = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);

        $restoreToken = random_string('md5');

        $modelUsers->update($user['id'], ['restore_code' => $restoreCode, 'restore_token' => $restoreToken]);

        $this->sendRestoreEmail($email, $restoreCode, $restoreToken);

        $response = [
            'success' => true,
            'message' => translate('please check your email for the restore link'),
            'redirect' => site_url('/restore/' . $restoreToken)
        ];
        return $this->response->setJSON($response);
    }

    public function sendRestoreEmail($email, $code, $token) {
        $emailConfig = \Config\Services::email();

        $config = new \Config\Email();

        $modelUsers = new UsersModel();
        
        $user = $modelUsers->where('email', $email)->first();

        $subject = translate('forgot your password?');
        $message = view('emails/restore_email', ['code' => $code, 'token' => $token, 'user' => $user]);

        $emailConfig->setFrom($config->fromEmail, $config->fromName);
        $emailConfig->setTo($email);
        $emailConfig->setSubject($subject);
        $emailConfig->setMessage($message);

        if ($emailConfig->send()) {
            return true;
        } else {
            return false;
        }
    }

    public function changeSubmit() {
        $modelUsers = new UsersModel();

        $validationRules = [
            'code' => [
                'label' => translate('code'),
                'rules' => 'required|exact_length[6]',
            ],
            'password' => [
                'label' => translate('new password'),
                'rules' => 'required|min_length[6]'
            ],
            'password_confirm' => [
                'label' => translate('password confirm'),
                'rules' => 'required|matches[password]'
            ]
        ];

        if (!$this->validate($validationRules)) {
            $errors = $this->validator->getErrors();
            $response = [
                'success' => false,
                'errors' => $errors
            ];
            return $this->response->setJSON($response);
        }

        $code = $this->request->getPost('code');
        $password = $this->request->getPost('password');
        $token = $this->request->getPost('token');

        $user = $modelUsers->where('restore_token', $token)->first();

        if (!$user) {
            $response = [
                'success' => false,
                'errors' => [
                    'code' => translate('invalid restore token')
                ]
            ];
            return $this->response->setJSON($response);
        }

        if ($code != $user['restore_code']) {
            $response = [
                'success' => false,
                'errors' => [
                    'code' => translate('invalid restore code')
                ]
            ];
            return $this->response->setJSON($response);
        }

        $newPassword = password_hash($password, PASSWORD_DEFAULT);

        $modelUsers->update($user['id'], ['password' => $newPassword, 'restore_code' => null, 'restore_token' => null]);

        $response = [
            'success' => true,
            'message' => translate('password changed successfully'),
            'redirect' => site_url('/signin')
        ];
        return $this->response->setJSON($response);
    }
}