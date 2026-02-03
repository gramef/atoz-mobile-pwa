<?php

namespace App\Exports;

use App\InterpreterJob;
use App\Job;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportData implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;
    protected $headings;
    public function __construct(array $data,array $headings)
    {
        $this->data=$data;
        $this->headings=$headings;
        
    }
    public function collection()
    {
        return new Collection($this->data);
        
    }
    public function headings(): array
    {
        return $this->headings;
    }
}
