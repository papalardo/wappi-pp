<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;
use App\MyClasses\Dialog;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Notifications\ConfirmationOfAttendanceResponse;
use App\Notifications\AttendanceErrorResponse;

class ChatWebhookController extends Controller
{
    public function main(Request $request) {

        if($request->input('messages.0.fromMe')) return;

        $message = $request->input('messages.0.body');
        $userPhone = explode('@', $request->input('messages.0.author'))[0];

        $res = json_decode($this->messages($request->input('messages.0.chatId')));
        
        if(count($res->messages) <=1) {
            $user = \App\Models\Customer::create([
                'name' => 'guest',
                'last_name' => 'guest',
                'email' => 'guest@guest.com',
                'phone' => $userPhone,
            ]);
            $user->dialog_config()->updateOrCreate(['type' => 'commom_dialog']);
        }
        
        $user = Customer::where('phone', $userPhone)->first();
        
        if($user && $user->dialog_config) {
            
            $dialog = (new Dialog($message))
                ->setSession($userPhone)
                ->send();

            if($dialog->getIntent() == 'apartment_rentals') {
                $user->notify(new ConfirmationOfAttendanceResponse($dialog->getBody()));
            }

            switch($user->dialog_config->type) {
                case 'confirmation_of_attendance': 
                    switch($dialog->getIntent()) {
                        case 'confirmation_of_attendance_yes':
                            $user->dialog_config()->update(['type' => 'commom_dialog']);
                        case 'confirmation_of_attendance_no':
                            $user->dialog_config()->update(['type' => 'commom_dialog']);
                        default:
                            $user->notify(new ConfirmationOfAttendanceResponse($dialog->getBody()));
                            break;
                    }
                // case 'commom_dialog':
                //     $user->notify(new ConfirmationOfAttendanceResponse($dialog->getBody()));
            }
        }

        return response()->json([
            'message' => 'Salvo'
        ]);
    }

    public function messages($chatId = '556181813368@c.us') {
        $client = new \GuzzleHttp\Client();
        $url = config('services.chatapi')['api_url'] . '/messages?last=true&chatId=' . $chatId . '&token=' . config('services.chatapi')['token'];
        return $this->getUrlContent($url);
    }

    function getUrlContent($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($httpcode>=200 && $httpcode<300) ? $data : false;
    }
}
