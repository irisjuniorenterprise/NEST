<?php

namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;

class NotificationService
{

    private const SERVER_API_KEY = 'AAAAtOlIIio:APA91bH_LYhZHXuY6V_ZI65MTfzXJDDHBIjB1IuUllJ5ISkTgG2bPFMbem5sQMuF2QAS04TQbuHY38ghSZP-VbAOzWaIZH72TimKlL4AjQwbg0xB5M8wpn5YpKKL3tRIWH_IvjtuutTV';
    public static function sendNotificationToEagles(string $title, string $body, array $departments, UserRepository $userRepository)
    {
        foreach ($departments as $department) {
            $departmentsNames[] = $department->getName();
            if (!in_array('All', $departmentsNames, true))
            {
                $users = $userRepository->findByDepartments($departments);
            }else{
                $users = $userRepository->findAll();
            }
            $tokens = [];
            foreach ($users as $user) {
                $tokens[] = $user->getTokenFcm();
            }
        }
        if (empty($tokens)) {
            return;
        }
        $tokensList = array_filter($tokens);
        //$token = 'cZvyu3izTiS69dI4Vlf6P_:APA91bErzP2mJI5xdfv1JsdzevOgJLJsdQr76P3T6Gfk3gMyz7RT88eGv3roW5mlIsRwgSW3ox3TBmRfwJDiQNd1iI2n69POFXJTevn977VH8gOx31jrGbdlW_0cj4YnyRdJ9KTRblP6';
        $data = [
            "registration_ids" => $tokensList,
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound"=> "default" // required for sound on ios
            ],
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . self::SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
    }
    public static function sendBlameNotification(string $title, string $body, User $user)
    {
        $data = [
            "registration_ids" => [$user->getTokenFcm()],
            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound"=> "default" // required for sound on ios
            ],
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . self::SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
    }
}