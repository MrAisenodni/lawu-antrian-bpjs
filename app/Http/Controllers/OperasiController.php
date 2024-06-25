<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Operasi;
use App\Models\Poli;
use App\Helpers\AppHelper;

class OperasiController extends Controller
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

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodepoli'  => 'required',
            'no_peserta'  => 'digits:13|required',
            'tanggal_operasi'  => 'date_format:Y-m-d|after_or_equal:today|required',
            'jenis_tindakan' =>'required',
            'nama_dokter' =>'required',
            'kode_booking' =>'required',
        ]);

        if ($validator->fails()) {
            return AppHelper::response_json(null, 201, $validator->errors()->first());
        }
        
         $existed_Operasis = Operasi::where('kode_poli', $request->kodepoli)
                                ->where('no_peserta', $request->no_peserta)
                                ->where('tanggal_operasi', $request->tanggal_operasi)
                                ->first();

        if ($existed_Operasis) {
            return AppHelper::response_json($existed_Operasis, 201, 'Nomor peserta sudah terdaftar');
        }
        
        $existed_poli = Poli::where('kode_poli', $request->kodepoli)
                                ->first();

        if (!$existed_poli) {
            return AppHelper::response_json($existed_poli, 201, 'Poli tidak terdaftar');
        }

        $operasi = new Operasi;

        //$operasi->kode_booking = $request->kodepoli . '-' . date('YmdHis');
        $operasi->kode_booking = $request->kode_booking;
        $operasi->kode_poli = $request->kodepoli;
        $operasi->nama_dokter = $request->nama_dokter;
        $operasi->no_peserta = $request->no_peserta;
        $operasi->tanggal_operasi = $request->tanggal_operasi;
        $operasi->jenis_tindakan = $request->jenis_tindakan;
        $operasi->namapoli = $existed_poli->nama_poli;

        $operasi->save();

        return AppHelper::response_json($operasi, 200, 'Ok');
    }

    public function recap_peserta(Request $request)
    {
		// $validator = Validator::make($request->all(), [
       //      'nopeserta'  => 'digits:13|required',
       // ]);

        
       //  if ($validator->fails()) {
       //     return AppHelper::response_json(null, 201, $validator->errors()->first());
      //  }
		
        $existed_operasi = Operasi::where('no_peserta', $request->nopeserta)
                                ->first();

        if (!$existed_operasi) {
            return AppHelper::response_json($existed_operasi, 401, 'Operasi dengan no peserta tersebut tidak ditemukan');
        }
        
        $list_operasi = Operasi::where('no_peserta', $request->nopeserta)
                                ->where('sudah_dilaksanakan', 0)
                                ->get();


        $response = array();
        foreach ($list_operasi as $key => $value) {
            $resp = [
                'kodebooking' => $value->kode_booking,
                'tanggaloperasi' => $value->tanggal_operasi,
                'jenistindakan' => $value->jenis_tindakan,
                'kodepoli' => $value->kode_poli,
                'namapoli' => $value->namapoli,
                'terlaksana' => $value->sudah_dilaksanakan
            ];
            $response[] = $resp;
        }
		$list_response['list'] = $response;
        return AppHelper::response_json($list_response, 200, 'Ok');
    }

    public function recap_schedule(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'tanggalawal'  => 'date_format:Y-m-d|after_or_equal:today|required',
            'tanggalakhir'  => 'date_format:Y-m-d|after_or_equal:today|required',
        ]);

        
         if ($validator->fails()) {
            return AppHelper::response_json(null, 201, $validator->errors()->first());
        }
        

        $list_operasi = Operasi::whereBetween('tanggal_operasi', [$request->tanggalawal, $request->tanggalakhir])
                                ->get();


        $response = array();
        foreach ($list_operasi as $key => $value) {
            $resp = [
                'kodebooking' => $value->kode_booking,
                'tanggaloperasi' => $value->tanggal_operasi,
                'jenistindakan' => $value->jenis_tindakan,
                'kodepoli' => $value->kode_poli,
                'namapoli' => $value->namapoli,
                'terlaksana' => $value->sudah_dilaksanakan,
                'nopeserta' => $value->no_peserta,
                'lastupdate' => strtotime($value->updated_at) * 1000,
            ];
            $response[] = $resp;
        }
		
		$list_response['list'] = $response;
        return AppHelper::response_json($list_response, 200, 'Ok');
    }
}
