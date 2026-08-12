<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Importacao extends Model
{
    protected $table = 'importacoes';

    protected $fillable = [
        'arquivo',
        'tamanho',
        'usuario_id',
        'status',
        'total_linhas',
        'importados',
        'invalidos',
        'duplicados',
        'colunas_ausentes',
        'linhas_invalidas',
        'erro_mensagem',
    ];

    protected function casts(): array
    {
        return [
            'colunas_ausentes' => 'array',
            'linhas_invalidas' => 'array',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function isolados(): HasMany
    {
        return $this->hasMany(Isolado::class);
    }
}
