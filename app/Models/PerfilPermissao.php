<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerfilPermissao extends Model
{
    protected $table = 'perfil_permissoes';

    protected $fillable = [
        'perfil',
        'acervo',
        'importar',
        'relatorios',
    ];

    protected function casts(): array
    {
        return [
            'acervo' => 'boolean',
            'importar' => 'boolean',
            'relatorios' => 'boolean',
        ];
    }

    public static function permite(?string $perfil, string $modulo): bool
    {
        if ($perfil === 'Administrador') {
            return true;
        }

        if (! in_array($modulo, ['acervo', 'importar', 'relatorios'], true)) {
            return false;
        }

        return (bool) static::query()->where('perfil', $perfil)->value($modulo);
    }
}
