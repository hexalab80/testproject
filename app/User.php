<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    //use Notifiable;
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'email', 'password','role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getImageAttribute($value)
    {
        if($value){
          return url($value);
        }
    }

     public function role()
    {
      return $this->belongsTo('App\Role');
    }

    /**
    * 1:n zu access token, we need to logout users
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    // public function accessTokens()
    // {
    //     return $this->hasMany(OauthAccessToken::class);
    // }

    //  public function getCreatedAtAttribute($value)
    // {
    //     if($value){
    //       return Carbon::createFromTimeStamp(strtotime($value))->diffForHumans();
    //     }
    // }
}
