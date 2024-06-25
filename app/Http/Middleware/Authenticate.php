<?php

namespace App\Http\Middleware;

use App\Helpers\AppHelper;
use App\Models\Poli;
use App\Models\Politutup;
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

        /* CHECK TOKEN EXPIRED */
        try {
            $payload = $this->auth->setToken($jwt);
            $payload = $payload->payload();
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return AppHelper::response_json(null, 201,'Token Expired');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            return AppHelper::response_json(null, 201,'Token Invalid');
        }

        /*  X-USERNAME TIDAK VALID */
        if($this->auth->setToken($jwt)->user()->username != $username)
        {
            return AppHelper::response_json(null,201, 'x-username tidak valid');
        }

        /* JIKA TOKEN INVALID */
        if (!$this->auth->setToken($jwt)->user() ) {
            /* JIKA USERNAME TIDAK VALID */
            return AppHelper::response_json(null, 201,'Unauthorized');
        }

        /* BACKDATE */
        if($request->has('tanggalperiksa'))
        {
            if(strtotime($request->tanggalperiksa) < strtotime(date('Y-m-d')))
            {
                return AppHelper::response_json(null, 201, 'Tanggal Periksa Tidak Berlaku');
            }
            
            /* REVISI LAGI DI COMMENT DULU */
            // if(strtotime($request->tanggalperiksa) == strtotime(date('Y-m-d'))){
            //     return AppHelper::response_json(null, 201, 'Tanggal Periksa Tidak Boleh Hari Ini');
            // }
        }

        /* CEK POLI */
        if($request->has('kodepoli') )
        {
            /* POLI TERDAFTAR TIDAK */
            $existed_poli = Poli::where('kode_poli', $request->kodepoli)
            ->first();

            if (!$existed_poli) {
                return AppHelper::response_json(null, 201, 'Jadwal Poli Tidak Ditemukan');
            }
        }
        
        return $next($request);
    }
}
