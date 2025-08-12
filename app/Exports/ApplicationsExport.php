<?php

namespace App\Exports;

use App\Models\Application;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ApplicationsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithColumnFormatting,
    ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Application::with(['category'])->orderBy('created_at', 'desc');

        // Apply filters from the request
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        if ($this->request->filled('category')) {
            $query->where('category_id', $this->request->category);
        }
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_email', 'like', "%{$search}%")
                    ->orWhere('student_phone', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Full Name',
            'Email',
            'Phone Number',
            'Application Status',
            'Selected Courses',
            'Category',
            'Application Date',
        ];
    }

    /**
     * @param mixed $application
     * @return array
     */
    public function map($application): array
    {
        // Get selected courses with fallback for deleted courses
        $selectedCourses = $this->getSelectedCoursesText($application);

        // Get category name with fallback for deleted categories
        $categoryName = $this->getCategoryName($application);

        // Format status with proper labels
        $status = $this->formatStatus($application->status);

        return [
            $application->student_name ?? 'N/A',
            $application->student_email ?? 'N/A',
            $application->student_phone ?? 'N/A',
            $status,
            $selectedCourses,
            $categoryName,
            $application->created_at ? $application->created_at->format('Y-m-d H:i') : 'N/A',
        ];
    }

    /**
     * Get selected courses text with proper handling for deleted courses
     */
    private function getSelectedCoursesText($application)
    {
        if (empty($application->selected_courses) || !is_array($application->selected_courses)) {
            return 'No courses selected';
        }

        $courseIds = $application->selected_courses;
        $existingCourses = Course::whereIn('id', $courseIds)->pluck('title', 'id')->toArray();

        $courseTexts = [];
        foreach ($courseIds as $courseId) {
            if (isset($existingCourses[$courseId])) {
                $courseTexts[] = $existingCourses[$courseId];
            } else {
                $courseTexts[] = 'Deleted Course (ID: ' . $courseId . ')';
            }
        }

        return implode(', ', $courseTexts);
    }

    /**
     * Get category name with fallback for deleted categories
     */
    private function getCategoryName($application)
    {
        if ($application->category) {
            return $application->category->name;
        } elseif ($application->category_id) {
            return 'Deleted Category (ID: ' . $application->category_id . ')';
        } else {
            return 'No Category';
        }
    }

    /**
     * Format status with proper labels
     */
    private function formatStatus($status)
    {
        switch (strtolower($status)) {
            case 'unregistered':
                return 'Pending';
            case 'registered':
                return 'Approved/Registered';
            case 'waiting':
                return 'Waiting List';
            case 'rejected':
                return 'Rejected';
            case 'promoted':
                return 'Promoted from Waiting';
            default:
                return ucfirst($status) ?? 'Unknown';
        }
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Data rows styling
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 1) {
            $sheet->getStyle('A2:G' . $lastRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_TOP,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'font' => [
                    'size' => 10,
                ],
            ]);

            // Alternate row colors for better readability
            for ($row = 3; $row <= $lastRow; $row += 2) {
                $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8F9FA'],
                    ],
                ]);
            }
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Set minimum row height for data rows
        if ($lastRow > 1) {
            for ($row = 2; $row <= $lastRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(20);
            }
        }

        return $sheet;
    }

    /**
     * Define column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20,  // Full Name
            'B' => 30,  // Email
            'C' => 15,  // Phone Number
            'D' => 18,  // Application Status
            'E' => 40,  // Selected Courses
            'F' => 20,  // Category
            'G' => 18,  // Application Date
        ];
    }

    /**
     * Column formatting
     */
    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
