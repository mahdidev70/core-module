<?php

namespace TechStudio\Core\app\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommentsExport implements FromCollection,WithHeadings
{

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            'شناسه',
            'متن',
            'وضعیت',
            'تاریخ ثبت',
            'وضعیت',
            'آی پی',
            'دلیل ردشدن',
            'پلتفرم',
            'مربوط به',
            'تعداد لایک ها'
        ];
    }
}
