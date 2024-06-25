<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Antrean;
use App\Models\Poli;
use App\Models\Politutup;
use App\Helpers\AppHelper;
use App\Models\Pasienbaru;
use Illuminate\Support\Facades\DB;

class AntreanController extends Controller
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
            'nik'  => 'digits:16|required',
            'nomorkartu'  => 'digits:13|required',
            'kodepoli'  => 'required',
            'tanggalperiksa'  => 'date_format:Y-m-d|after_or_equal:today|required',
            'jenisrequest' => 'required|integer|between:1,2',
            'jenisreferensi' => 'required|integer|between:1,2',
            'polieksekutif' => 'required|integer|between:0,1',
        ]);

        
         if ($validator->fails()) {
            return AppHelper::response_json(null, 201, $validator->errors()->first());
        }
        
        $existed_antrean = Antrean::where('kode_poli', $request->kodepoli)
                                ->where('no_kartu', $request->nomorkartu)
                                ->where('tanggal_periksa', $request->tanggalperiksa)
                                ->whereRaw('LOWER(status) != LOWER("batal")')
                                ->first();

        if ($existed_antrean) {
            return AppHelper::response_json($existed_antrean, 401, 'Anda sudah terdaftar antrean');
        }

        $existed_politutup = Politutup::where('kode_poli', $request->kodepoli)
                                ->where('tanggal_off', $request->tanggalperiksa)
                                ->first();

        if ($existed_politutup) {
             return AppHelper::response_json($existed_politutup, 401, 'Di tanggal tersebut Poli Tutup');
        }


        $existed_poli = Poli::where('kode_poli', $request->kodepoli)
                                ->first();

        if (!$existed_poli) {
            return AppHelper::response_json($existed_antrean, 401, 'Poli Tidak Ditemukan');
        }
        
        $count_antrean = Antrean::where('tanggal_periksa', $request->tanggalperiksa)
                                ->count();

        $last_antrean = Antrean::where('tanggal_periksa', $request->tanggalperiksa)
                                ->orderBy('created_at', 'desc')
                                ->first();

        if ($last_antrean) {
            $estimasi = $count_antrean * 15;
            $stamp = strtotime("+$estimasi minutes", strtotime($last_antrean->tanggal_periksa));
        } else {
            $stamp = strtotime("+15 minutes", time());
        }                          
        

        $antrean = new Antrean;

        $next_antrean = str_pad(($count_antrean + 1), 3, "0", STR_PAD_LEFT);
        $antrean->no_antrean = $request->jkn. '' . $next_antrean;
        $antrean->kode_booking = $request->kodepoli . '-' . date('YmdHis');
        $antrean->kode_poli = $request->kodepoli;
        $antrean->nik = $request->nik;
        $antrean->tanggal_periksa = $request->tanggalperiksa;
        $antrean->no_kartu = $request->nomorkartu;
        $antrean->no_telp = $request->notelp;
        $antrean->no_referensi = $request->nomorreferensi;
        $antrean->jenis_referensi = $request->jenisreferensi;
        $antrean->jenis_request = $request->jenisrequest;
        $antrean->poli_eksekutif = $request->polieksekutif;
        $antrean->estimasi_dilayani = date('Y-m-d H:i:s', $stamp);

        $antrean->save();

        $resp = [
            'nomorantrean' => $antrean->no_antrean,
            'kodebooking' => $antrean->kode_booking,
            'jenisantrean' => $antrean->jenis_request,
            'estimasidilayani' => $stamp * 1000,
            'namapoli' => $existed_poli->nama_poli,
            'namadokter' => $existed_poli->nama_dokter,
        ];

        return AppHelper::response_json($resp, 200, 'Ok');
    }

    public function recap(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'tanggalperiksa'  => 'date_format:Y-m-d|after_or_equal:today|required',
        ]);

        
         if ($validator->fails()) {
            return AppHelper::response_json(null, 201, $validator->errors()->first());
        }
        

        $existed_poli = Poli::where('kode_poli', $request->kodepoli)
                                ->first();

        if (!$existed_poli) {
            return AppHelper::response_json($existed_poli, 401, 'Poli Tidak Ditemukan');
        }
        
        $count_antrean = Antrean::where('kode_poli', $request->kodepoli)
                                ->where('tanggal_periksa', $request->tanggalperiksa)
                                ->where('poli_eksekutif', $request->polieksekutif)
                                ->count();
        
        $count_dilayani = Antrean::where('kode_poli', $request->kodepoli)
                                ->where('tanggal_periksa', $request->tanggalperiksa)
                                ->where('poli_eksekutif', $request->polieksekutif)
                                ->where('sudah_dilayani', 1)
                                ->count();

        $last_antrean = Antrean::where('kode_poli', $request->kodepoli)
                                ->where('tanggal_periksa', $request->tanggalperiksa)
                                ->where('poli_eksekutif', $request->polieksekutif)
                                ->orderBy('created_at', 'desc')
                                ->first();

        
        $resp = [
            'namapoli' => $existed_poli->nama_poli,
            'totalantrean' => $count_antrean,
            'jumlahterlayani' => $count_dilayani,
            'lastupdate' => empty($last_antrean) ? null : strtotime($last_antrean->created_at) * 1000,
        ];

        return AppHelper::response_json($resp, 200, 'Ok');
    }

    /* DONE */
    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodepoli' => 'required',
            'kodedokter' => 'required',
            'tanggalperiksa' => 'required|date_format:Y-m-d',
            'jampraktek' => 'required',
        ], [
            'tanggalperiksa.date_format'  => 'Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd'
        ]);

        if($validator->fails())
        {
            return AppHelper::response_json(null, 201, $validator->errors()->first());           
        }

        /* CEK JAM */
        $jam = $request->jampraktek;
        $jam = explode('-',trim($jam));

        $jam_awal = trim($jam[0]);
        $jam_akhir = trim($jam[1]);

        $jam_awal = str_replace('.',':',$jam_awal);
        $jam_akhir = str_replace('.',':',$jam_akhir);

        /* CEK POLI HARI INI */
        $poli_hari_ini = Poli::where('kode_poli',$request->kodepoli)
                            ->where('kodedokter',$request->kodedokter)
                            ->whereDate('JAMMULAI',$request->tanggalperiksa)
                            ->whereTime('JAMMULAI', '<=', $jam_awal)
                            ->whereTime('JAMSELESAI', '>=', $jam_akhir)
                            ->first();


        if(!$poli_hari_ini)
        {
            return AppHelper::response_json(null, 400, 'Antrean Tidak Ditemukan');           
        }

        $total_antrean = Antrean::where('tanggal_periksa',$request->tanggalperiksa)
        ->where('kode_poli',$request->kodepoli)
        ->where('sudah_dilayani',0)
        ->count();

        $total_antrean_jkn = Antrean::where('tanggal_periksa',$request->tanggalperiksa)
        ->where('kode_poli',$request->kodepoli)
        ->where('sudah_dilayani',0)
        ->whereRaw('LOWER(jkn) = "jkn"')
        ->count();

        $total_antrean_non_jkn = Antrean::where('tanggal_periksa',$request->tanggalperiksa)
        ->where('kode_poli',$request->kodepoli)
        ->where('sudah_dilayani',0)
        ->whereRaw('LOWER(jkn) != "jkn"')
        ->count();
                        

        // $total_antrean_jkn = $total_antrean->where('jkn',"JKN")->count();

        $pangil = DB::table('panggil')->select('panggiljkn')->get();

        $sisa_antrean = Antrean::where('tanggal_periksa',$request->tanggalperiksa)
        ->where('kode_poli',$request->kodepoli)
        ->where('sudah_dilayani',0)
        ->where('status','check_in')
        ->count();

        $antrean_panggil = Antrean::where('tanggal_periksa',$request->tanggalperiksa)
        ->where('kode_poli',$request->kodepoli)
        ->where('sudah_dilayani',0)
        ->where('status','check_in')
        ->orderBy('no_antrean', 'asc')
        ->first();

        $kuota_jkn = $poli_hari_ini->kuotajkn;
        $sisa_kuota_jkn = $kuota_jkn - $total_antrean_jkn; //Sisakuotajknb= kuotajkn - totalpasien
        $kuota_non_jkn = $poli_hari_ini->kuotanonjkn;
        $sisa_kuota_non_jkn = $poli_hari_ini->kuotanonjkn - $total_antrean_non_jkn; //Sisakuotanonjkn dibuat 0 saja pak

        $antrean = Antrean::where('kode_poli',$request->kodepoli)
                ->where('kode_dokter',$request->kodedokter)
                ->where('tanggal_periksa',$request->tanggalperiksa);
                // ->where('jam_praktek',$request->jampraktek);

        $data = $antrean->first();
                

        if(!$data)
        {
            return AppHelper::response_json(null, 201, 'Antrean tidak ditemukan');
        }
        
        
        $response = [
            'namapoli' => $poli_hari_ini->nama_poli,
            'namadokter' => $poli_hari_ini->nama_dokter,
            'totalantrean' => $total_antrean,
            'sisaantrean' => $sisa_antrean,
            'antreanpanggil' => $antrean_panggil ? $antrean_panggil->no_antrean:'-',
            'sisakuotajkn' => $sisa_kuota_jkn,
            'kuotajkn' => $kuota_jkn,
            'sisakuotanonjkn' => $sisa_kuota_non_jkn,
            'kuotanonjkn' => $kuota_non_jkn,
            'keterangan' => $data->keterangan,
        ];

        return AppHelper::response_json($response, 200, 'Ok');
    }

    /* DONE */
    public function ambil(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomorkartu' => 'required|digits:13|numeric',
            'nik' => 'required|digits:16|numeric',
            'nohp' => 'required',
            'kodepoli' => 'required',
            'norm' => 'required',
            'tanggalperiksa' => 'required|date_format:Y-m-d',
            'kodedokter' => 'required',
            'jampraktek' => 'required',
            'jeniskunjungan' => 'required',
            'nomorreferensi' => 'required',
        ],[
            'nomorkartu.required' => 'Nomor Kartu Belum Diisi',
            'nomorkartu.digits' => 'Format Nomor Kartu Tidak Sesuai',
            'nomorkartu.numeric' => 'Format Nomor Kartu Tidak Sesuai',
            'nik.required' => 'NIK Belum Diisi',
            'nik.digits' => 'Format NIK Tidak Sesuai',
            'nik.numeric' => 'Format NIK Tidak Sesuai',
            'nohp.required' => 'No Hp Belum Diisi',
            'kodepoli.required' => 'Kode Poli Belum Diisi',
            'norm.required' => 'Kode Poli Belum Diisi',
            'kodedokter.required' => 'Kode Dokter Belum Diisi',
            'jampraktek.required' => 'Jam Praktek Belum Diisi',
            'tanggalperiksa.required' => 'Tanggal Periksa Belum Diisi',
            'tanggalperiksa.date_format' => 'Format Tanggal Tidak Sesuai, format yang benar adalah yyyy-mm-dd',
            'jeniskunjungan.required' => 'Jenis Kunjungan Belum Diisi',
            'nomorreferensi.required' => 'No Referensi Belum Diisi',
        ]);

        if($validator->fails())
        {
            return AppHelper::response_json(null, 201, $validator->errors()->first());           
        }

        /* CEK JAM */
        $jam = $request->jampraktek;
        $jam = explode('-',trim($jam));

        $jam_awal = trim($jam[0]);
        $jam_akhir = trim($jam[1]);

        $jam_awal = str_replace('.',':',$jam_awal);
        $jam_akhir = str_replace('.',':',$jam_akhir);

        /* CEK PASIEN */
        $pasien = Pasienbaru::where('norm',$request->norm)->first();
        if(!$pasien){
            return AppHelper::response_json(null,202,'Data pasien ini tidak ditemukan, silahkan Melakukan Registrasi Pasien Baru');
        }

        /* CEK POLI SEBELUM JAM */
        $cek_poli_sebelum_jam = Poli::where('kode_poli',$request->kodepoli)
                                ->whereDate('JAMMULAI',$request->tanggalperiksa)
                                ->whereRaw('DATE_FORMAT(JAMMULAI,"%H:%i") <= ?', $jam_awal)
                                ->first();
        
        if(!$cek_poli_sebelum_jam){
            $poli = Poli::whereDate('JAMMULAI',$request->tanggalperiksa)->where('kode_poli',$request->kodepoli)->first();
            $message = 'Pendaftaran Ke Poli '.$poli->nama_poli.' Baru Buka Jam  '.date('H:i', strtotime($poli->JAMMULAI)).'';
            return AppHelper::response_json(null,201,$message);
        }

        /* CEK POLI MELEBIHI JAM */
        $cek_poli_melebihi_jam = Poli::where('kode_poli',$request->kodepoli)
                                ->whereDate('JAMSELESAI',$request->tanggalperiksa)
                                ->whereRaw('DATE_FORMAT(JAMSELESAI,"%H:%i") > ?', $jam_akhir)
                                ->first();
        
        // return $cek_poli_melebihi_jam;
        if(!$cek_poli_melebihi_jam){
            $poli = Poli::whereDate('JAMSELESAI',$request->tanggalperiksa)->where('kode_poli',$request->kodepoli)->first();
            $message = 'Pendaftaran Ke Poli '.$poli->nama_poli.' Sudah Tutup Jam  '.date('H:i', strtotime($poli->JAMSELESAI));
            return AppHelper::response_json(null,201,$message);
        }

    
        /* CEK JADWAL DOKTER */
        // $dokter = Poli::where('kodedokter',$request->kodedokter)->first();
        // if($dokter){
            $cek_jadwal_dokter = Poli::where('kodedokter',$request->kodedokter)
                                ->whereDate('JAMMULAI',$request->tanggalperiksa)
                                ->whereRaw('DATE_FORMAT(JAMMULAI,"%H:%i") <= ?', $jam_awal)
                                ->whereRaw('DATE_FORMAT(JAMSELESAI,"%H:%i") > ?', $jam_akhir)
                                ->first();
    
            

            if(!$cek_jadwal_dokter){
                $dokter = Poli::where('kodedokter',$request->kodedokter)->first();
                $message = 'Jadwal Dokter Tidak Ditemukan';
                if($dokter){
                       $message = 'Jadwal Dokter '.$dokter->nama_dokter.' Tersebut Belum Tersedia, Silahkan Reschedule Tanggal dan Jam Praktek Lainnya';
                }
                return AppHelper::response_json(null,201,$message);
            }
        // }

        
        

        $telah_daftar = Antrean::where('kode_poli',$request->kodepoli)
        ->where('nik',$request->nik)
        ->where('tanggal_periksa',$request->tanggalperiksa)
        ->where('status','!=','batal')
        ->count();

        if($telah_daftar)
        {
            return AppHelper::response_json(null, 201,'Nomor Antrean Hanya Dapat Diambil 1 Kali Pada Tanggal Yang Sama');
        }

        $count_antrean = Antrean::where('tanggal_periksa', $request->tanggalperiksa)
                                ->count();

        $last_antrean = Antrean::where('tanggal_periksa', $request->tanggalperiksa)
                                ->orderBy('created_at', 'desc')
                                ->first();

        if ($last_antrean) {
            $estimasi = $count_antrean * 15;
            $stamp = strtotime("+$estimasi minutes", strtotime($last_antrean->tanggal_periksa));
        } else {
            $stamp = strtotime("+15 minutes", time());
        }     

        $data = new Antrean();

        $next_antrean = str_pad(($count_antrean + 1), 3, "0", STR_PAD_LEFT);
        $data->no_antrean = $request->jkn. '' . $next_antrean;
        $data->kode_booking = $request->kodepoli . '-' . date('YmdHis');
        $data->no_kartu = $request->nomorkartu;
        $data->nik = $request->nik;
        $data->no_telp = $request->nohp;
        $data->kode_poli = $request->kodepoli;
        $data->no_rm = $request->norm;
        $data->tanggal_periksa = $request->tanggalperiksa;
        $data->kode_dokter = $request->kodedokter;
        

        $data->jam_praktek = $request->jampraktek;
        $data->no_referensi = $request->nomorreferensi;
        $data->jenis_referensi = $request->jeniskunjungan;
        $data->jenis_request = 1;
        $data->poli_eksekutif = 0;
        
        $data->estimasi_dilayani = date('Y-m-d H:i:s', $stamp);

        $data->save();

        $response = [
            'nomorantrean'=>$data->no_antrean,
            'angkaantrean'=>$next_antrean,
            'kodebooking'=>$data->kode_booking,
            'norm'=>$data->norm,
            'namapoli'=>$data->kode_poli,
            'namadokter'=>$data->kode_dokter,
            'estimasidilayani'=>strtotime($data->estimasi_dilayani),
            'sisakuotajkn'=>0,
            'kuotajkn'=>0,
            'sisakuotanonjkn'=>0,
            'kuotanonjkn'=>0,
            'keterangan'=>'',
        ];

        return AppHelper::response_json($response, 200, 'Ok');
    }

    /* DONE */
    public function sisa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodebooking' => 'required'
        ],[
            'kodebooking.required' => 'Kode Booking Belum Diisi'
        ]);

        if($validator->fails())
        {
            return AppHelper::response_json(null, 201, $validator->errors()->first());           
        }

        $kode_booking = explode('-',$request->kodebooking);
        $kode_poli = $kode_booking[0];

        // $tanggal = substr($kode_booking[1],0,8);
        // $tanggal_periksa = substr($tanggal,0,4).'-'.substr($tanggal,4,2).'-'.substr($tanggal,6,2);

        $data = Antrean::where('kode_booking',$request->kodebooking)
        ->first();
        
        $poli = Poli::where('kode_poli',$data->kode_poli)->first();
        $dokter = Poli::where('kodedokter',$data->kode_dokter)->first();
        
        /* ANTREAN TIDAK DITEMUKAN */
        if(!$data)
        {   
            return AppHelper::response_json(null,201, 'Antrean Tidak Ditemukan');
        }

        $sisa_antrean = Antrean::where('tanggal_periksa',$data->tanggal_periksa)
        ->where('kode_poli',$kode_poli)
        ->where('sudah_dilayani',0)
        ->where('status','check_in')
        ->count();

        $antrean_panggil = Antrean::where('tanggal_periksa',$data->tanggal_periksa)
        ->where('kode_poli',$kode_poli)
        ->where('sudah_dilayani',0)
        ->where('status','check_in')
        ->orderBy('no_antrean', 'asc')
        ->first();

        $response = [
            'nomorantrean'=>$data->no_antrean,
            'namapoli'=> $poli->nama_poli ?? '-',
            'namadokter'=> $dokter->nama_dokter ?? '-',
            'sisaantrean'=> $sisa_antrean,
            'antreanpanggil'=> $antrean_panggil ? $antrean_panggil->no_antrean:'-',
            'waktutunggu'=>strtotime($data->estimasi_dilayani),
            'keterangan'=>$data->keterangan,
        ];

        return AppHelper::response_json($response, 200, 'Ok');
    }

    /* DONE */
    public function batal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodebooking' => 'required',
            'keterangan' => 'required',
        ]);

        if($validator->fails())
        {
            return AppHelper::response_json(null, 201, $validator->errors()->first());           
        }

        $data = Antrean::where('kode_booking',$request->kodebooking);
        
        /* TIDAK DITEMUKAN */
        if(!$data->first())
        {
            return AppHelper::response_json(null,201, 'Antrean Tidak Ditemukan');
        }

        /* SUDAH DILAYANI */
        if($data->first()->sudah_dilayani)
        {
            return AppHelper::response_json(null,201, 'Pasien Sudah Dilayani, Antrean Tidak Dapat Dibatalkan');
        }
        
        /* SUDAH DIBATALKAN */
        if($data->where('status','batal')->first())
        {
            
            return AppHelper::response_json(null,201, 'Antrean Tidak Ditemukan atau Sudah Dibatalkan');
        }
        
        $antrean = Antrean::where('kode_booking',$request->kodebooking)->first();

        
        $antrean->keterangan = $request->keterangan;
        $antrean->status = 'batal';
        $antrean->save();

        return AppHelper::response_json(null, 200, 'Ok');
    }

    /* DONE */
    public function check_in(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kodebooking' => 'required',
            'waktu' => 'required',
        ]);

        if($validator->fails())
        {
            return AppHelper::response_json(null, 201, $validator->errors()->first());           
        }

        $data = Antrean::where('kode_booking',$request->kodebooking)->first();
        if(!$data)
        {
            return AppHelper::response_json(null,201, 'Gagal. Antrean Tidak Ditemukan');
        }

        $data->waktu = $request->waktu;
        $data->keterangan = '';
        $data->status = 'check_in';
        $data->save();

        return AppHelper::response_json(null, 200, 'Ok');
    }

    /* DONE */
    public function info_pasien_baru(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomorkartu' => 'required|unique:pasienbaru,nomorkartu|digits:13|numeric',
            'nik' => 'required|unique:pasienbaru,nik|digits:16|numeric',
            'nomorkk' => 'required',
            'nama' => 'required',
            'jeniskelamin' => 'required',
            'tanggallahir' => 'required|date_format:Y-m-d|before_or_equal:today',
            'nohp' => 'required',
            'alamat' => 'required',
            'kodeprop' => 'required',
            'namaprop' => 'required',
            'kodedati2' => 'required',
            'namadati2' => 'required',
            'kodekec' => 'required',
            'namakec' => 'required',
            'kodekel' => 'required',
            'namakel' => 'required',
            'rw' => 'required',
            'rt' => 'required',

        ],[
            'nomorkartu.required' => 'Nomor Kartu Belum Diisi',
            'nomorkartu.digits' => 'Format Nomor Kartu Tidak Sesuai',
            'nomorkartu.numeric' => 'Format Nomor Kartu Tidak Sesuai',
            'nomorkartu.unique' => 'Data Peserta Sudah Pernah Dientrikan',
            'nik.required' => 'NIK Belum Diisi',
            'nik.digits' => 'Format NIK Tidak Sesuai',
            'nik.numeric' => 'Format NIK Tidak Sesuai',
            'nik.unique' => 'Data Peserta Sudah Pernah Dientrikan',
            'nomorkk.required' => 'Nomor KK Belum Diisi',
            'nama.required' => 'Nama Belum Diisi',
            'jeniskelamin.required' => 'Jenis Kelamin Belum Dipilih',
            'tanggallahir.required' => 'Tanggal Lahir Belum Diisi',
            'nohp.required' => 'No Hp Belum Diisi',
            'alamat.required' => 'Alamat Belum Diisi',
            'kodeprop.required' => 'Kode Propinsi Belum Diisi',
            'namaprop.required' => 'Nama Propinsi Belum Diisi',
            'kodedati2.required' => 'Kode Dati 2 Belum Diisi',
            'namadati2.required' => 'Dati 2 Belum Diisi',
            'kodekec.required' => 'Kode Kecamatan Belum Diisi',
            'namakec.required' => 'Kecamatan Belum Diisi',
            'kodekel.required' => 'Kode Kelurahan Belum Diisi',
            'namakel.required' => 'Kelurahan Belum Diisi',
            'rw.required' => 'RW Belum Diisi',
            'rt.required' => 'RT Belum Diisi',
            'tanggallahir.date_format' => 'Format Tanggal Lahir Tidak Sesuai',
            'tanggallahir.before_or_equal' => 'Format Tanggal Lahir Tidak Sesuai',
        ]);

        if($validator->fails())
        {
            return AppHelper::response_json(null, 201, $validator->errors()->first());           
        }

        $count_pasien = Pasienbaru::get()->count();

        $pasienbaru = new Pasienbaru();

        $pasienbaru->nomorkartu = $request->nomorkartu;
        $pasienbaru->norm = str_pad(($count_pasien + 1), 5, "0", STR_PAD_LEFT);
        $pasienbaru->nik = $request->nik;
        $pasienbaru->nomorkk = $request->nomorkk;
        $pasienbaru->nama = $request->nama;
        $pasienbaru->jeniskelamin = $request->jeniskelamin;
        $pasienbaru->tanggallahir = $request->tanggallahir;
        $pasienbaru->nohp = $request->nohp;
        $pasienbaru->alamat = $request->alamat;
        $pasienbaru->kodeprop = $request->kodeprop;
        $pasienbaru->namaprop = $request->namaprop;
        $pasienbaru->kodedati2 = $request->kodedati2;
        $pasienbaru->namadati2 = $request->namadati2;
        $pasienbaru->kodekec = $request->kodekec;
        $pasienbaru->namakec = $request->namakec;
        $pasienbaru->kodekel = $request->kodekel;
        $pasienbaru->namakel = $request->namakel;
        $pasienbaru->rw = $request->rw;
        $pasienbaru->rt = $request->rt;

        $pasienbaru->save();

        $response = [
            'norm'=>$pasienbaru->norm,
        ];
        return AppHelper::response_json($response, 200, 'Harap datang ke admisi untuk melengkapi data rekam medis');
    }


}
