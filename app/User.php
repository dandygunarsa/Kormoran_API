<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'tb_user';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'user_name', 'email', 'password', 'jenis_kelamin','pict', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'access_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     
    public function pertanyaan()
    {
        return $this->hasMany('App\Pertanyaan');
    }

    public function jawaban(){
        return $this->hasMany('App\Jawaban');
    }

    public function countJawaban($id){
        $count = Jawaban::where('user_id','=',$id)->get();
        $totalCount = $count->count();
        return $totalCount;
    }

    public function countPertanyaan($id){
        $count = Pertanyaan::where('user_id','=',$id)->get();
        $totalCount = $count->count();
        return $totalCount;
    }


}