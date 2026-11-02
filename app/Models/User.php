<?php

namespace App\Models;

use App\Mail\OTPCodeMail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'otp_code',
        'otp_expires_at',
        'otp_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected function casts(): array
    {
        return [
            'otp_verified' => 'boolean',
            'otp_expires_at' => 'datetime'
        ];
    }

    public function generateOTP()
    {
        $this->update([
            'otp_code' => rand(100000, 999999),
            'otp_expires_at' => now()->addMinutes(10),
            'otp_verified' => false
        ]);

        return $this->otp_code;
    }

    public function isOTPValid($code)
    {
        return $this->otp_code === $code &&
            now()->lt($this->otp_expires_at);
    }

    public function sendOTPNotification()
    {
        $otpCode = $this->generateOTP();
        Mail::to($this->email)->send(new OTPCodeMail($otpCode));
    }

    public function store() {
        return $this->hasOne(Store::class, 'owner_id', 'id');
    }
}
