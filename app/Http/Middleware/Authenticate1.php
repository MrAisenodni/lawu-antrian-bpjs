<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Models\Poli;
use App\Models\Politutup;
use App\Models\Antrean;
use Closure;
use JWTAuth;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        
        $jwt = $request->header('x-token');

        $username = $request->header('x-username');
        
        if(!$username)
        {
            return AppHelper::response_json(null, 404, 'x-username tidak ditemukan');
        }

        /* JIKA TOKEN TIDAK ADA */
        if(!$jwt)
        {
            return AppHelper::response_json(null, 404, 'Token tidak ditemukan');
        }

        /* BACKDATE */
        if($request->has('tanggalperiksa'))
        {
            if(strtotime($request->tanggalperiksa) < strtotime(date('Y-m-d')))
            {
                return AppHelper::response_json(null, 201, 'Tanggal Periksa Tidak Berlaku');
            }
        }

        /* CEK POLI */
        if($request->has('kodepoli') )
        {
            /* POLI TERDAFTAR TIDAK */
            $existed_poli = Poli::where('kode_poli', $request->kodepoli)
            ->first();

            if (!$existed_poli) {
                return AppHelper::response_json(null, 201, 'Poli Tidak Ditemukan');
            }
            
            /* POLI TUTUP */
            $existed_politutup = Politutup::where('kode_poli', $request->kodepoli)
                    ->where('tanggal_off', $request->tanggalperiksa)
                    ->first();

            if ($existed_politutup) {
                return AppHelper::response_json(null, 201, 'Pendaftaran ke Poli Ini Sedang Tutup');
            }   
            
            /* daftar tgl sama */
            $existed_Antrean = Antrean::where('no_kartu', $request->nomorkartu)
                    ->where('tanggal_periksa', $request->tanggalperiksa)
                    ->first();

            if ($existed_Antrean) {
                return AppHelper::response_json(null, 201, 'Pasien sudah terdaftar');
            }   

        }

        /* CHECK TOKEN EXPIRED */
        try {
            $payload = $this->auth->setToken($jwt);
            $payload = $payload->payload();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return AppHelper::response_json(null, 201,'Token Expired');
        }

        /*  X-USERNAME TIDAK VALID */
        if($this->auth->setToken($jwt)->user()->username != $username)
        {
            return AppHelper::response_json(null,201, 'x-username tiadk valid');
        }
        
        
        /* JIKA TOKEN INVALID */
        if (!$this->auth->setToken($jwt)->user() ) {
            /* JIKA USERNAME TIDAK VALID */
            return AppHelper::response_json(null, 201,'Unauthorized');
        }


        return $next($request);
    }
}
