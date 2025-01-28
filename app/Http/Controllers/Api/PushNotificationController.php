<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pusher\PushNotifications\PushNotifications;

class PushNotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        // Récupère les informations du client
        $interest = $request->input('interest');
        $title = $request->input('title');
        $body = $request->input('body');

        // Initialise Pusher Beams
        $beamsClient = new PushNotifications(array(
            "instanceId" => env('PUSHER_BEAMS_INSTANCE_ID'),
            "secretKey" => env('PUSHER_BEAMS_SECRET_KEY'),
        ));

        // Envoie la notification
        $publishResponse = $beamsClient->publishToInterests(
            array($interest), // Par exemple : 'hello'
            array(
                "web" => array("notification" => array("title" => $title, "body" => $body)),
                "fcm" => array("notification" => array("title" => $title, "body" => $body)),
                "apns" => array("aps" => array("alert" => array("title" => $title, "body" => $body))),
            )
        );

        return response()->json($publishResponse);
    }
}
