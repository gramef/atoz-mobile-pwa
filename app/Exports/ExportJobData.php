<?php

namespace App\Exports;

use App\InterpreterJob;
use App\TranslatorJob;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportJobData implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Combine InterpreterJobs and TranslatorJobs into one collection.
     */
    public function collection()
    {
        $interpreterJobs = InterpreterJob::hasEnabledUser()
            ->with(['toLanguage', 'agent.user'])
            ->filter($this->filters)
            ->get()
            ->map(function ($job) {
                return [
                    'type' => 'Interpreter',
                    'id' => $job->id,
                    'agent' => $job->agent->user->full_name ?? 'DELETED USER',
                    'date' => $job->appointment_date,
                    'language' => $job->toLanguage->name,
                    'additional_info' => $job->department, // Add other interpreter-specific fields
                ];
            });

        $translatorJobs = TranslatorJob::hasEnabledUser()
            ->with(['fromLanguage', 'toLanguage', 'agent.user'])
            ->filter($this->filters)
            ->get()
            ->map(function ($job) {
                return [
                    'type' => 'Translator',
                    'id' => $job->id,
                    'agent' => $job->agent->user->full_name ?? 'DELETED USER',
                    'date' => $job->target_date,
                    'language' => $job->fromLanguage->name . ' → ' . $job->toLanguage->name,
                    'additional_info' => $job->word_count, // Add other translator-specific fields
                ];
            });

        return $interpreterJobs->merge($translatorJobs);
    }

    /**
     * Map the data for each row.
     */
    public function map($row): array
    {
        return [
            $row['type'],
            $row['id'],
            $row['agent'],
            $row['date'],
            $row['language'],
            $row['additional_info'],
        ];
    }

    /**
     * Define the headings for the exported file.
     */
    public function headings(): array
    {
        return [
            'Job Type',
            'Job Ref',
            'Agent Name',
            'Date',
            'Language(s)',
            'Additional Info',
        ];
    }

    /**
     * Apply styles to the exported sheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ]);

        // Auto-size columns
        foreach (range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set default row height
        $sheet->getDefaultRowDimension()->setRowHeight(20);
    }
}