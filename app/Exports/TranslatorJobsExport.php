<?php

namespace App\Exports;

use App\TranslatorJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class TranslatorJobsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }
public function styles(Worksheet $sheet)
{
    // Style header row
    $sheet->getStyle('A1:Z1')->applyFromArray([
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

    // Ensure text wrapping for all columns
    $sheet->getStyle('A:Z')->getAlignment()->setWrapText(true);

    // Auto-size columns (if necessary)
    foreach (range('A', 'Z') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Set default row height
    $sheet->getDefaultRowDimension()->setRowHeight(20); // Adjust as needed
}


    /**
     * Query the jobs with necessary filters and relationships.
     */
    public function query()
    {
        return TranslatorJob::hasEnabledUser()
            ->with([
                'toLanguage',
                'matchedAgents',
                'client.user',
                'agent.user',
                'client.organisation.company',
                'cancellation',
            ])
            ->visibleToUser(auth()->user())
            ->filter($this->filters)
            ->orderBy('target_date', 'DESC');
    }

    /**
     * Map the data for each row.
     */
    public function map($job): array
    {
        $agent = $job->agent->user->full_name ?? 'DELETED USER';
if($job->affidavit){
    $job->affidavit="Yes";
}else
{
    $job->affidavit="No";
}
if($job->affirmation){
    $job->affirmation="Yes";
}else
{
    $job->affirmation="No";
}
        return [
            $job->id,
            $agent,
            $job->target_date,
            $job->word_count,
            $job->affidavit,
            $job->affirmation,
            $job->fromLanguage->name,
            $job->toLanguage->name,
            $job->department,
            $job->address_line_1,
            $job->address_line_2,
            $job->county,
            $job->postcode,
            $job->client_reference,
            '', // Timesheet Status
            '', // Timesheet Submission Date & Time
            '', // Extended Time Requested
            '', // Basic Cost
            '', // Additional Costs
            '', // Penalty Deduction
            '', // Total Cost
        ];
    }

    /**
     * Define the headings for the exported file.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Translator Name',
            'Date of Session',
            'Word Count',
            'Affidavit',
            'Affirmation',
            'From Language',
            'To Language',
            'Delivery Address 1',
            'Delivery Address 2',
            'County',
            'Postcode',
            'Provider Invoice Number',
            'Timesheet Status',
            'Timesheet Submission Date & Time',
            'Extended Time Requested',
            'Basic Cost',
            'Additional Costs',
            'Penalty Deduction',
            'Total Cost'
        ];
    }
}
