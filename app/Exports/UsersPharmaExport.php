<?php

namespace App\Exports;

use App\Models\UsersPharma;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersPharmaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return UsersPharma::select(
            'first_name',
            'last_name',
            'email',
            'phone_number',
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
        ];
    }
}
