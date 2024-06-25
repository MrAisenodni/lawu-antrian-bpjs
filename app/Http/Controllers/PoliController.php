<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poli;
use App\Helpers\AppHelper;

class PoliController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $poli = Poli::all();
        return AppHelper::response_json($poli, 200, 'Ok');
    }

    public function create(Request $request)
    {
        return Poli::insert($request->all());

        $this->validate($request, [
            'kode_poli' => 'required',
            'nama_poli' => 'required',
            'nama_dokter' => 'required',
        ]);

        try {

            $poli = new Poli;
            $poli->kode_poli = $request->input('kode_poli');
            $poli->nama_poli = $request->input('nama_poli');
            $poli->nama_dokter = $request->input('nama_dokter');

            $poli->save();

            return AppHelper::response_json($poli, 200, 'Poli successfully reated');

        } catch (\Exception $e) {
            //return error message
            return AppHelper::response_json(null, 200, 'Failed created poli');
        }
    }
}
