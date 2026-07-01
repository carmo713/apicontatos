<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contato extends Model
{
    protected $fillable = [
        'user_id',
        'nome',
        'telefone',
        'email',
        'favorito',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
