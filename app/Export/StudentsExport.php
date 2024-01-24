<?php

namespace TechStudio\Core\app\Export;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection,WithHeadings
{

    protected $students;

    public function __construct($students)
    {
        return $this->students = $students;
    }
    public function collection()
    {
        return $this->students;
    }

    public function headings(): array
    {
        return [
          /*  'شناسه',*/
            'نام و نام خانوادگی',
            'نوع کاربر',
            'دوره های اجباری',
            'دوره های تکمیل شده',
            'دوره های ذخیره شده',
        ];
    }
}
