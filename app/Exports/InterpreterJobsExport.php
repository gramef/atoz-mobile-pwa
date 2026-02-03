<?php

namespace App\Exports;

use App\InterpreterJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InterpreterJobsExport implements  FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Query the jobs with necessary filters and relationships.
     */
    public function query()
    {
        return InterpreterJob::hasEnabledUser()
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
            ->orderBy('appointment_date', 'DESC');
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
     * Map the data for each row.
     */
    public function map($job): array
    {
      //  dd($job);
        $agent = $job->agent->user->full_Name ?? 'DELETED USER';

        return [
            $job->id,
            $agent,
            $job->appointment_date,
            $job->start_time,
            $job->end_time,
            $job->duration_hours . ':' . $job->duration_minutes,
            '', // Actual Start Time
            '', // Actual End Time
            '', // Actual Duration
            $job->toLanguage->name,
            $job->getGenderName(),
            $job->department,
            $job->address_line_1,
            $job->address_line_2,
            '', // Delivery Address 4
            '', // Delivery Address 5
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
            'Interpreter Name',
            'Date of Session',
            'Scheduled Start Time',
            'Scheduled End Time',
            'Scheduled Duration',
            'Actual Start Time',
            'Actual End Time',
            'Actual Duration',
            'Language',
            'Gender Requirement',
            'Delivery Address 1',
            'Delivery Address 2',
            'Delivery Address 3',
            'Delivery Address 4',
            'Delivery Address 5',
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
