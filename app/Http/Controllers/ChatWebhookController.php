<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Notifications\ConfirmationOfAttendanceResponse;
use App\Notifications\AttendanceErrorResponse;

class ChatWebhookController extends Controller
{
    public function main(Request $request) {

        if($request->input('messages.0.fromMe')) return;
        
        $this->messages($request->input('messages.0.chatId'));
        // if($request->input('messages.0.chatId'))

        $userPhone = explode('@', $request->input('messages.0.author'))[0];
        $message = $request->input('messages.0.body');
        $user = App\Models\Customer::where('phone', $userPhone)->first();
        if($user && $user->dialog_config) {
            // Preparar string
            $message = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($message)));
            $message = strtolower($message);
    
            switch($user->dialog_config->type) {
                case 'confirmation_of_attendance': 
                    if(strpos($message, 'sim') > -1 || $message == 1) {
                        $user->notify(new ConfirmationOfAttendanceResponse("Perfeito 🎉.\nJá confirmei aqui na agenda sua presença!"));
                        $user->dialog_config()->update(['type' => 'commom_dialog']);
                    } elseif (strpos($message, 'nao') > -1 || $message == 2) {
                        $user->notify(new ConfirmationOfAttendanceResponse('Ok.. 😞, talvez da próxima então.'));
                        $user->dialog_config()->update(['type' => 'commom_dialog']);
                    } else {
                        $user->notify(new AttendanceErrorResponse());
                    }
            }
        }
        return response()->json([
            'message' => 'Salvo'
        ]);
    }

    public function messages($chatId) {
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', config('chatapi.api_url') . '/messages?token=' . config('chatapi.token') . '&chatId=' . $chatId);
        \Log::info($res);
    }
}
