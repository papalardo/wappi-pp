<?php

namespace App\Http\Controllers\API\v1;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Notifications\ConfirmationOfAttendanceResponse;
use App\Notifications\AttendanceErrorResponse;

class ChatWebhookController extends Controller
{
    public function main(Request $request) {

        $res = json_decode($this->messages($request->input('messages.0.chatId')));
        
        \Log::info(count($res->messages));
        
        if(count($res->messages) <=1) {
            $userPhone = explode('@', $request->input('messages.0.author'))[0];
            $user = \App\Models\Customer::create([
                'name' => 'guest',
                'last_name' => 'guest',
                'email' => 'dlsap@dlsap.com',
                'phone' => $userPhone,
            ]);
            $user->notify(new ConfirmationOfAttendanceResponse("Olá. Aqui é o assitente virtual do Pablo.\n*Você quer falar sobre os aluguéis na Vila Planalto ?*\n_Digite:_ \n*1* - Para sim e *2* - Para não"));
        }
        
        return;
        
        if($request->input('messages.0.fromMe')) return;
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
