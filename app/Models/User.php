<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public $timestamps = true;

    protected $fillable = [
        'name',
        'email',
        'cuil',
        'tipo',
        'password',
        'panel_actual',
        'entidad',
        'estado',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    // Atributo temporal para controlar la verificación de email
    protected $shouldSendEmailVerification = false;

    /**
     * Establece si se debe enviar la notificación de verificación de email.
     *
     * @param bool $value
     * @return void
     */
    public function setShouldSendEmailVerification(bool $value)
    {
        $this->shouldSendEmailVerification = $value;
    }

    /**
     * Verifica si el correo electrónico del usuario ya fue confirmado.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Marca el correo electrónico como verificado.
     *
     * @return void
     */
    public function markEmailAsVerified()
    {
        $this->email_verified_at = now();
        $this->save();
    }

    /**
     * Envía una notificación de restablecimiento de contraseña.
     *
     * @param string $token
     * @return void
     * @throws ValidationException
     */
    public function sendPasswordResetNotification($token)
    {
        if ($this->estado == 2) {
            throw ValidationException::withMessages([
                'email' => ['No tienes permiso para restablecer la contraseña. Tu cuenta se encuentra eliminada.']
            ]);
        }

        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Envía una notificación de verificación de correo electrónico.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail($this));
    }

    /**
     * Reenvía una notificación de verificación de correo electrónico.
     *
     * @return void
     */
    public function resendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail($this));
    }

    public function lastLogin()
    {
        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->orderBy('last_activity', 'desc')
            ->value('last_activity');
    }

    public function emailVerifiedDate()
    {
        return $this->email_verified_at 
        ? \Carbon\Carbon::parse($this->email_verified_at)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d H:i') 
        : null;
    }

    public function createdAtDate()
    {
        // Verifica si la fecha de creación está disponible y la formatea
        return $this->created_at
            ? \Carbon\Carbon::parse($this->created_at)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d H:i')
            : 'No disponible';
    }
}
