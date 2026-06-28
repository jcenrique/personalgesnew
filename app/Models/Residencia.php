<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Residencia extends Model
{
    protected $fillable = ['name', 'zona_id'];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
