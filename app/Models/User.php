<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'occupation',
        'connect'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function wallet(){
        return $this->hasOne(Wallet::class);
    }

    public function projects(){
        return $this->hasMany(Project::class,'client_id','id')->orderByDesc('id');
    }

    public function proposals(){
        return $this->hasMany(ProjectApplicant::class,'freelancer_id','id')->orderByDesc('id');
    }

    public function hasAppliedToProject($projectId){
        return ProjectApplicant::where('project_id',$projectId)
        ->where('freelancer_id',$this->id)
        ->exists();
    }
}
