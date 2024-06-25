<?php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class AppHelper {

    public static function response_json($data = null, $code = 200, $message = 'Ok')
    {
        $res = [
            'response' => $data,
            'metadata' => [
                'message'   => $message,
                'code'      => $code,
            ],
        ];

        if(!$data)
        {
            unset($res['response']);
        }
        
        return response()->json($res, $code);
    }

    public static function indo_day($index){
        $days = [
            'minggu',
            'senin',
            'selasa',
            'rabu',
            'kamis',
            'jumat',
            'sabtu'
        ];

        if(isset($days[$index])){
            return $days[$index];
        }

        return 'jumat';
    }

}