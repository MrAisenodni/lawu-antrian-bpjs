<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model {

    protected $table = 'polis';


    public static function findByCode($kode_poli)
    {
        return self::where('kode_poli', $kode_poli)->first();

    }

}
