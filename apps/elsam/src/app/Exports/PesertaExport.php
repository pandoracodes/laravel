<?php

namespace App\Exports;

use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PesertaExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithColumnFormatting
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        // Load relasi paket dan program di dalamnya
        return Peserta::with(['paket.program'])
            ->filter($this->filters);
    }

    public function headings(): array
    {
        return [
            'No. Pendaftaran',
            'Nama Peserta',
            'Nama Orang Tua',
            'Email',
            'No. Telepon',
            'No. Wali',
            'NIK (KTP)',
            'Program Kursus',
            'Status',
            'Tanggal Daftar',
        ];
    }

    public function map($peserta): array
    {
        return [
            $peserta->no_pendaftaran,
            $peserta->nama_peserta,
            $peserta->nama_orang_tua,
            $peserta->email,
            $peserta->no_telepon,
            $peserta->no_wali,
            $peserta->no_ktp,
            // Akses program melalui paket
            $peserta->paket?->program?->nama_program ?? 'N/A',
            $peserta->status_peserta,
            $peserta->tanggal_pendaftaran ? $peserta->tanggal_pendaftaran->format('d-m-Y') : '-',
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Memastikan kolom No Telepon (E), Wali (F), dan KTP (G) terbaca sebagai Text agar angka nol tidak hilang
            'E' => NumberFormat::FORMAT_TEXT, 
            'F' => NumberFormat::FORMAT_TEXT, 
            'G' => NumberFormat::FORMAT_TEXT, 
        ];
    }
}