<?php
 
namespace App\Controllers;
 
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
 
class Authentication extends BaseController
{
    use ResponseTrait;

    public function login(){
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $login_with = $this->request->getPost('login_with');
        $name = $this->request->getPost('name');

        $model = new UserModel();
        $data = $model->where('email',$email)->first();

        if($data){

            if($login_with !== "email"){
                $responseData = [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'data'=> $data
                ];
                return $this->respond($responseData, 200);
            }

            //Login email and password
            if ($data['password'] === $password) {
                $responseData = [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'data'=> $data
                ];
                return $this->respond($responseData, 200);
            } else {
                
                $responseData = [
                    'status' => 'error',
                    'message' => 'Invalid password',
                ];
                return $this->respond($responseData, 401);
            }
        }
        else {

            if($login_with == "google" || $login_with == "facebook"){
                //insert new one
                $newData = ['email' => $email, 'password' => '', 'name' => $name];
                $model->insert($newData);
                $responseData = [
                    'status' => 'success',
                    'message' => 'Login successful',
                    'data'=> $newData
                ];
                return $this->respond($responseData, 200);

            }else{    
                $responseData = [
                    'status' => 'error',
                    'message' => 'Invalid email',
                ];
                return $this->respond($responseData, 401);
            }

        }
    }

    public function register(){
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $name = $this->request->getPost('name');

        $data = ['email' => $email,
                'password' => $password,
                'name' => $name];

        $model = new UserModel();
        $model->insert($data);
        $response = [
            'status'   => 201,
            'error'    => null,
            'messages' => [
                'success' => 'Pendaftaran berhasil'
            ]
        ];
        return $this->respondCreated($response);
    }

    public function forgotPassword(){
        $email = $this->request->getPost('email');

        $model = new UserModel();

        $data = $model->where('email',$email)->first();

        if($data){
            
            $updatedPassword = $data['password'];
            if(empty($updatedPassword)){
                $updatedPassword = '12345678';
                $model->update($data['id'],['password'=> $updatedPassword]);
            }

            $data = ['password' => $updatedPassword];

            $responseData = [
                'status' => 'success',
                'message' => '',
                'data'=> $data
            ];
            return $this->respond($responseData,200);
        }
        else{
            $responseData = [
                'status' => 'error',
                'message' => 'Invalid email',
            ];
            return $this->respond($responseData, 401);
        }
        
    }

    public function uploadImage()
    {
        $rules = [
            'image' => 'uploaded[image]|mime_in[image,image/jpg,image/jpeg,image/png]',
        ];
    
        if (!$this->validate($rules)) {
            return $this->fail($this->validator->getErrors());
        }

        $image = $this->request->getFile('image');

        if ($image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            $image->move(ROOTPATH . 'public/uploads', $newName);

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Image uploaded successfully',
                'file_name' => $newName
            ]);
        } else {
            return $this->fail($image->getErrorString());
        }
    }
}