<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operasi extends Model {

    public function poli()
    {
        return $this->belongsTo('App\Models\Poli', 'kode_poli', 'kode_poli');
    }

}
