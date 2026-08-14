<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IsoladosExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(protected Collection $registros)
    {
    }

    public function collection(): Collection
    {
        return $this->registros;
    }

    public function headings(): array
    {
        return [
            'Código', 'Gênero', 'Espécie', 'Origem', 'Meio de cultivo',
            'Data de coleta', 'Conservação', 'Local', 'Armazenamento', 'Autor',
        ];
    }

    public function map($registro): array
    {
        return [
            $registro->codigo,
            $registro->genero,
            $registro->especie,
            $registro->origem,
            $registro->meio_cultivo,
            $registro->data?->format('d/m/Y'),
            $registro->conservacao,
            $registro->local,
            $registro->armazenamento,
            $registro->autor,
        ];
    }
}
