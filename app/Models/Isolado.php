<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Isolado extends Model
{
    protected $fillable = [
        'codigo',
        'genero',
        'especie',
        'origem',
        'meio_cultivo',
        'data',
        'conservacao',
        'local',
        'armazenamento',
        'autor',
        'importacao_id',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'date',
        ];
    }

    public function especieCompleta(): string
    {
        return trim($this->genero.' '.$this->especie);
    }

    public static function valoresDistintos(string $coluna): \Illuminate\Support\Collection
    {
        return static::query()
            ->whereNotNull($coluna)
            ->where($coluna, '!=', '')
            ->distinct()
            ->orderBy($coluna)
            ->pluck($coluna);
    }
}
