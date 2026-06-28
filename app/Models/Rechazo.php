<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

class Rechazo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'rechazos';

    protected $fillable = [
        'user_id',
        'rechazable_type',
        'rechazable_id',
        'fecha_disfrute',
        'razon',
    ];

    protected $casts = [
        'fecha_disfrute' => 'date',
    ];

    /**
     * Relación con el usuario que realizó la solicitud rechazada.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación morph hacia el concepto (Saturday, AdditionalDay, etc.)
     */
    public function rechazable(): MorphTo
    {
        return $this->morphTo();
    }
}
