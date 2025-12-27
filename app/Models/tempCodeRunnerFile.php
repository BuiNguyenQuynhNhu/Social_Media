<?php
public function createAccount(array $user, array $profile){
        $db = \Config\Database::connect();
        $profileModel = new Profile();
        $uuid = $this->generateUUID();

        $db->transStart();
            $user['id'] = $uuid;
            $this->insert($user);

            $profile['id'] = $uuid;
            $profileModel->insert($profile);
        $db->transComplete();
        if ($db->transStatus() === false)
            return ['status' => false, 'message' => 'System error, can not create account'];
        return ['status' => true, 'id' => $uuid];
    }