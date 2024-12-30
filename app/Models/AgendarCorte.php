<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendarCorte extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'data_agendamento',
        'hora_agendamento',
        'observacao',
    ];

    public function usuario(){
        return $this->belongsTo(User::class);
    }
}
