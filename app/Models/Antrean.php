<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antrean extends Model {

    protected $table = 'antreans';

    public function poli()
    {
        return $this->belongsTo(Poli::class, 'kode_poli', 'kode_poli');
    }

}
