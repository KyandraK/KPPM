<?php

namespace App\Exports;

use App\Models\History;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class HistoriesExport implements FromQuery, WithHeadings, WithMapping, WithTitle, WithEvents, ShouldAutoSize
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = History::query()->with(['vehicle', 'request']);

        if (!empty($this->filters['vehicle_data']['wheels'])) {
            $query->whereHas('vehicle', function ($q) {
                $q->where('wheels', $this->filters['vehicle_data']['wheels']);
            });
        }

        if (!empty($this->filters['vehicle_data']['model'])) {
            $query->whereHas('vehicle', function ($q) {
                $q->where('model', 'like', '%' . $this->filters['vehicle_data']['model'] . '%');
            });
        }

        if (!empty($this->filters['vehicle_data']['license_plate'])) {
            $query->whereHas('vehicle', function ($q) {
                $q->where('license_plate', 'like', '%' . $this->filters['vehicle_data']['license_plate'] . '%');
            });
        }

        if (!empty($this->filters['created_at']['created_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['created_at']['created_from']);
        }

        if (!empty($this->filters['created_at']['created_until'])) {
            $query->whereDate('created_at', '<=', $this->filters['created_at']['created_until']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            // 'ID',
            'Tanggal Peminjaman',
            'Waktu Peminjaman',
            'Nama Peminjam',
            'Jenis Kendaraan',
            'Model/Tipe',
            'No. Polisi',
            'Status Peminjaman',
        ];
    }

    public function map($history): array
    {
        return [
            // $history->id,
            $history->created_at->format('Y-m-d'),
            $history->created_at->format('H:i:s'),
            optional($history->request->user)->name,
            optional($history->vehicle)->wheels,
            optional($history->vehicle)->model,
            optional($history->vehicle)->license_plate,
            optional($history->request)->status,
        ];
    }

    public function title(): string
    {
        return 'Histories Export';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->insertNewRowBefore(1, 3);

                $period = $this->getPeriod();
                $sheet->setCellValue('A1', 'Daftar Peminjaman Kendaraan Dinas');
                $sheet->setCellValue('A2', 'Pusat Pengelolaan Komplek Kemayoran');
                $sheet->setCellValue('A3', 'Periode Peminjaman: ' . $period);

                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A3:G3');

                $sheet->getStyle('A1:A3')->getFont()->setBold(true);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');

                $sheet->getRowDimension('1')->setRowHeight(20);
                $sheet->getRowDimension('2')->setRowHeight(20);
                $sheet->getRowDimension('3')->setRowHeight(20);
            },
        ];
    }

    private function getPeriod()
    {
        $query = $this->query();
        $minDate = $query->min('created_at');
        $maxDate = $query->max('created_at');

        $minDateFormatted = $minDate ? (new \DateTime($minDate))->format('Y-m-d') : '-';
        $maxDateFormatted = $maxDate ? (new \DateTime($maxDate))->format('Y-m-d') : '-';

        return $minDateFormatted . ' s.d ' . $maxDateFormatted;
    }
}
