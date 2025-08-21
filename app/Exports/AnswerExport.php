<?php

namespace App\Exports;

use App\Models\Answer;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AnswerExport implements FromCollection, WithMapping, WithHeadings, WithEvents
{

    protected $grade;
    protected $subject;
    protected $answers;

    public function __construct($grade, $subject)
    {
        $this->grade = $grade;
        $this->subject = $subject;
    }

    public function collection()
    {
        $answers =  Answer::with('student')->where('grade', $this->grade);
        if ($this->subject != "all") {
            $answers = $answers->where('subject', $this->subject);
        }
        return $this->answers =  $answers->get();
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function map($answer): array
    {
        static $index = 1;
        return [
            $index++,
            $answer->student->name ?? '',
            $answer->student->email ?? '',
            $answer->grade,
            $answer->subject,
            $answer->topic,
            $answer->total_questions,
            $answer->total_answered,
            $answer->correct_answers,
            "{$answer->score} %",
            $answer->performance,
            Carbon::parse($answer->created_at)->format('d F Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            ['Report Lexile Level Grade ' . $this->grade],
            [],
            ["No", "Name", "Email", "Grade", "subject", "Topic", "Total", "", "", "Score", "Performance", "Date Time"], // Empty row (optional)
            ['', '', '', '', '', '', 'Question', 'Answered', 'Correct', '', '', '']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge title row
                $sheet->mergeCells('A1:K1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(15);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Format header row (row 3)
                $sheet->getStyle('A3:L4')->getFont()->setBold(true);
                $sheet->getStyle('A3:L4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A3:L4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A3:L4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                $sheet->mergeCells('A3:A4'); //No
                $sheet->mergeCells('B3:B4'); //Name
                $sheet->mergeCells('C3:C4'); //email
                $sheet->mergeCells('D3:D4'); //Grade
                $sheet->mergeCells('E3:E4'); //Subject
                $sheet->mergeCells('F3:F4'); //Topic
                $sheet->mergeCells('G3:I3'); //Total
                $sheet->mergeCells('J3:J4'); //Score
                $sheet->mergeCells('K3:K4'); //Performance
                $sheet->mergeCells('L3:L4'); //Submitted Date

                $endRow = 4 + $this->answers->count();
                $sheet->getStyle("A5:L{$endRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
        ];
    }
}
