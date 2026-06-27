<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;

use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;

use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail,  LdapAuthenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use AuthenticatesWithLdap; // <--- ESTO ES LO QUE FALTA
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',

        'category',
        'email',
        'password',
        'locale',
        'status',
        'codigo_agente',
        'notify',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'notify' => 'boolean',
    ];


    public function scopeNotifiable($query)
    {

        return $query->where('notify', true);
    }

    public function getLdapGuidColumn(): string
    {
        return 'email';
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean'
        ];
    }



    public function canAccessPanel(Panel $panel): bool
    {


        if ($panel->getId() === 'admin') {
            $is_admin = $this->roles()
                ->pluck('name')
                ->contains(fn($role) => in_array($role, [
                    'super_admin',
                    'admin',

                ]));
            return $is_admin;
        }
        // Si intenta entrar al panel de la app (u otros)
        if ($panel->getId() === 'app') {


            return  true; // O cualquier otra validación que necesites
        }

        return true;
    }


    /* * Relación con el modelo Sabado */

    public function sabados()
    {
        return $this->hasMany(Sabado::class);
    }

    /* * Relación con el modelo Additionalday */
    public function additionaldays()
    {
        return $this->hasMany(Additionalday::class);
    }

     /* * Relación con el modelo Additionalday */
    public function companydays()
    {
        return $this->hasMany(Additionalday::class);
    }

    /* * Relación con el modelo Computo */
    public function computos()
    {
        return $this->hasMany(Computo::class);
    }

    /* * Relación con el modelo Computo */
    public function reconocimientos()
    {
        return $this->hasMany(Reconocimiento::class);
    }


    public function disfrutes()
    {
        return $this->hasMany(\App\Models\Disfrute::class);
    }


    public function rechazos()
    {
        return $this->hasMany(\App\Models\Rechazo::class);
    }

    /* * Relación con el modelo Computo */
    public function disponibilidades()
    {
        return $this->hasMany(Disponibilidad::class);
    }

    public function residencias()
    {
        return $this->belongsToMany(Residencia::class);
    }

    public function inspector()
    {
        return $this->belongsToMany(Inspeccion::class);
    }


    public function tecnico()
    {
        return $this->belongsToMany(Inspeccion::class);
    }


    public function zonas(): BelongsToMany
    {
        return $this->belongsToMany(Zona::class);
    }



    public function trainingActions()
    {
        return $this->belongsToMany(TrainingAction::class, 'training_action_user')
            ->withPivot(['attended',  'certificate_path'])
            ->withTimestamps();
    }

    /**
     * ¿Puede gestionar usuarios?
     */
    public function canManageUsers(): bool
    {
        return $this->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * ¿Puede gestionar una zona concreta?
     */
    public function canManageZona(Zona $zona): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return $this->zonas->contains($zona);
    }

    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('status', true);
        });
    }
}
