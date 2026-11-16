<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    private const REPORT_TIMEZONE = 'Asia/Manila';
    private const DATABASE_TIMEZONE = 'UTC';

    private function categories(): array
    {
        return [
            'tourist_accounts' => [
                'title' => 'Tourist Accounts',
                'description' => 'User growth, demographics, and account status.',
                'table' => 'profiles',
                'date' => 'created_at',
                'headers' => [
                    'Registered At',
                    'Account Status',
                ],
                'columns' => [
                    'created_at',
                    'is_active',
                ],
            ],

            'tourist_spots' => [
                'title' => 'Tourist Spots',
                'description' => 'Tourist destinations and active location records.',
                'table' => 'tourist_spots',
                'date' => 'created_at',
                'headers' => [
                    'Spot Name',
                    'Category',
                    'Address',
                    'Status',
                    'Added At',
                ],
                'columns' => [
                    'name',
                    'category',
                    'address',
                    'is_active',
                    'created_at',
                ],
            ],

            'incident_reports' => [
                'title' => 'Incident Reports',
                'description' => 'Incident submissions and review status.',
                'table' => 'incidents',
                'date' => 'created_at',
                'headers' => [
                    'Incident Type ID',
                    'Status',
                    'Submitted At',
                    'Reviewed At',
                ],
                'columns' => [
                    'type_id',
                    'status',
                    'created_at',
                    'reviewed_at',
                ],
            ],

            'crime_records' => [
                'title' => 'Crime Records',
                'description' => 'Historical crime data and severity information.',
                'table' => 'crime_data',
                'date' => 'crime_date',
                'headers' => [
                    'Crime Date',
                    'Crime Type ID',
                    'Severity',
                    'Incident Count',
                    'Source',
                ],
                'columns' => [
                    'crime_date',
                    'type_id',
                    'severity',
                    'crime_count',
                    'source',
                ],
            ],

            'safety_alerts' => [
                'title' => 'Safety Alerts',
                'description' => 'Active and historical safety alert records.',
                'table' => 'safety_alerts',
                'date' => 'created_at',
                'headers' => [
                    'Title',
                    'Alert Level',
                    'Status',
                    'Created At',
                    'Starts At',
                    'Ends At',
                ],
                'columns' => [
                    'title',
                    'alert_level',
                    'is_active',
                    'created_at',
                    'starts_at',
                    'ends_at',
                ],
            ],

            'trip_history' => [
                'title' => 'Trip History',
                'description' => 'Tourist journeys and completed trip activity.',
                'table' => 'trip_history',
                'date' => 'started_at',
                'headers' => [
                    'Started At',
                    'Completed At',
                    'Status',
                    'Distance (m)',
                    'Duration (sec)',
                ],
                'columns' => [
                    'started_at',
                    'completed_at',
                    'status',
                    'distance_meters',
                    'duration_seconds',
                ],
            ],
        ];
    }

    private function authorized(): bool
    {
        return session()->has('admin_user');
    }

    private function isActive($value): bool
    {
        return in_array(
            $value,
            [true, 1, '1', 't'],
            true
        );
    }

    /**
     * Convert a timestamp from the database to
     * Philippine Standard Time for display.
     *
     * Timestamp values are assumed to represent UTC
     * unless they already contain an explicit offset.
     */
    private function formatReportTimestamp($value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return Carbon::parse(
            (string) $value,
            self::DATABASE_TIMEZONE
        )
            ->setTimezone(self::REPORT_TIMEZONE)
            ->format('M d, Y h:i A') . ' PST';
    }

    /**
     * Format a crime calendar date without shifting
     * it to a different day.
     */
    private function formatCrimeDate($value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return Carbon::parse(
            (string) $value,
            self::REPORT_TIMEZONE
        )->format('M d, Y');
    }

    private function formatReportValue(
        string $column,
        $value
    ): string {
        if ($value === null) {
            return '—';
        }

        if ($column === 'is_active') {
            return $this->isActive($value)
                ? 'Active'
                : 'Inactive';
        }

        $timestampColumns = [
            'created_at',
            'reviewed_at',
            'starts_at',
            'ends_at',
            'started_at',
            'completed_at',
        ];

        if (in_array($column, $timestampColumns, true)) {
            return $this->formatReportTimestamp($value);
        }

        if ($column === 'crime_date') {
            return $this->formatCrimeDate($value);
        }

        return (string) $value;
    }

    private function prepareReport(Request $request): array
    {
        $categories = $this->categories();

        $validated = $request->validate([
            'category' => [
                'nullable',
                Rule::in(array_keys($categories)),
            ],
            'period' => [
                'nullable',
                Rule::in([
                    '7_days',
                    '30_days',
                    'quarterly',
                    'annual',
                ]),
            ],
            'start_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'end_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],
            'visualization' => [
                'nullable',
                'boolean',
            ],
        ]);

        $category = $validated['category']
            ?? 'incident_reports';

        $period = $validated['period']
            ?? '30_days';

        $startDate = $validated['start_date']
            ?? null;

        $endDate = $validated['end_date']
            ?? null;

        $hasFilters = $request->hasAny([
            'category',
            'period',
            'start_date',
            'end_date',
        ]);

        $visualization = $hasFilters
            ? $request->boolean('visualization')
            : true;

        if (($startDate === null) !== ($endDate === null)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'start_date' =>
                    'Enter both a start date and an end date for a custom range.',
            ]);
        }

        // All report periods are interpreted using
        // Philippine calendar dates.
        $today = Carbon::today(self::REPORT_TIMEZONE);

        if ($startDate !== null && $endDate !== null) {
            $from = Carbon::parse(
                $startDate,
                self::REPORT_TIMEZONE
            )->startOfDay();

            $to = Carbon::parse(
                $endDate,
                self::REPORT_TIMEZONE
            )->endOfDay();

            $periodLabel = 'Custom Date Range';
        } else {
            $to = $today->copy()->endOfDay();

            $from = match ($period) {
                '7_days' => $today->copy()
                    ->subDays(6)
                    ->startOfDay(),

                '30_days' => $today->copy()
                    ->subDays(29)
                    ->startOfDay(),

                'quarterly' => $today->copy()
                    ->firstOfQuarter()
                    ->startOfDay(),

                'annual' => $today->copy()
                    ->startOfYear()
                    ->startOfDay(),
            };

            $periodLabel = match ($period) {
                '7_days' => 'Past 7 Days',
                '30_days' => 'Past 30 Days',
                'quarterly' => 'Current Quarter to Date',
                'annual' => 'Current Year to Date',
            };
        }

        $config = $categories[$category];

        /*
         * Crime dates are calendar dates.
         * They should not be timezone-shifted.
         *
         * Other categories use UTC timestamp
         * boundaries corresponding to the
         * selected Philippine calendar dates.
         */
        if ($category === 'crime_records') {
            $query = DB::table($config['table'])
                ->whereBetween('crime_date', [
                    $from->format('Y-m-d'),
                    $to->format('Y-m-d'),
                ]);
        } else {
            $utcFrom = $from->copy()
                ->setTimezone(self::DATABASE_TIMEZONE)
                ->format('Y-m-d H:i:sP');

            $utcTo = $to->copy()
                ->setTimezone(self::DATABASE_TIMEZONE)
                ->format('Y-m-d H:i:sP');

            $query = DB::table($config['table'])
                ->whereBetween($config['date'], [
                    $utcFrom,
                    $utcTo,
                ]);
        }

        if ($category === 'tourist_accounts') {
            $query->where('role', 'tourist');
        }

        $records = $query
            ->select($config['columns'])
            ->orderBy($config['date'], 'desc')
            ->get();

        /*
         * All formatted rows are shared by:
         * - Browser preview
         * - PDF export
         * - Excel export
         */
        $rows = $records->map(
            function ($record) use ($config) {
                $row = [];

                foreach ($config['columns'] as $column) {
                    $value = $record->{$column} ?? null;

                    $row[] = $this->formatReportValue(
                        $column,
                        $value
                    );
                }

                return $row;
            }
        )->all();

        $summary = $this->buildSummary(
            $category,
            $records
        );

        return [
            'reportCategories' => collect($categories)
                ->map(fn ($item) => [
                    'title' => $item['title'],
                    'description' => $item['description'],
                ])
                ->all(),

            'selectedCategory' => $category,
            'selectedTitle' => $config['title'],

            'period' => $period,
            'periodLabel' => $periodLabel,

            'startDate' => $startDate,
            'endDate' => $endDate,

            'fromDisplay' => $from->format('M d, Y'),
            'toDisplay' => $to->format('M d, Y'),

            'reportTimezone' => self::REPORT_TIMEZONE,

            'visualization' => $visualization,

            'summary' => $summary,

            'headers' => $config['headers'],
            'rows' => $rows,

            'recordCount' => count($rows),
        ];
    }

    private function buildSummary(
        string $category,
        $records
    ): array {
        $total = $records->count();

        $metric = fn ($label, $value) => [
            'label' => $label,
            'value' => (int) $value,
        ];

        return match ($category) {
            'tourist_accounts' => [
                $metric(
                    'Total Tourist Accounts',
                    $total
                ),
                $metric(
                    'Active Tourist Accounts',
                    $records->filter(
                        fn ($r) => $this->isActive($r->is_active)
                    )->count()
                ),
            ],

            'tourist_spots' => [
                $metric(
                    'Total Tourist Spots Added',
                    $total
                ),
                $metric(
                    'Currently Active Spots',
                    $records->filter(
                        fn ($r) => $this->isActive($r->is_active)
                    )->count()
                ),
            ],

            'incident_reports' => [
                $metric(
                    'Total Incident Reports',
                    $total
                ),
                $metric(
                    'Pending',
                    $records->where('status', 'Pending')->count()
                ),
                $metric(
                    'Approved',
                    $records->where('status', 'Approved')->count()
                ),
                $metric(
                    'Rejected',
                    $records->where('status', 'Rejected')->count()
                ),
            ],

            'crime_records' => [
                $metric(
                    'Crime Record Entries',
                    $total
                ),
                $metric(
                    'Reported Crime Count',
                    $records->sum('crime_count')
                ),
                $metric(
                    'High Severity Records',
                    $records->where('severity', 'High')->count()
                ),
            ],

            'safety_alerts' => [
                $metric(
                    'Total Safety Alerts',
                    $total
                ),
                $metric(
                    'Currently Active Alerts',
                    $records->filter(
                        fn ($r) => $this->isActive($r->is_active)
                    )->count()
                ),
            ],

            'trip_history' => [
                $metric(
                    'Total Trips Started',
                    $total
                ),
                $metric(
                    'Completed Trips',
                    $records->where('status', 'Completed')->count()
                ),
            ],
        };
    }

    public function index(Request $request)
    {
        if (!$this->authorized()) {
            return redirect()->route('admin.login');
        }

        $report = $this->prepareReport($request);

        // Keep the preview manageable.
        $report['previewRows'] = array_slice(
            $report['rows'],
            0,
            20
        );

        return view(
            'admin.reports.index',
            $report
        );
    }

    public function exportPdf(Request $request)
    {
        if (!$this->authorized()) {
            return redirect()->route('admin.login');
        }

        $report = $this->prepareReport($request);

        $filename = 'citypulse-' .
            Str::slug($report['selectedTitle']) .
            '-' .
            now(self::REPORT_TIMEZONE)->format('Ymd-His') .
            '.pdf';

        return Pdf::loadView(
            'admin.reports.pdf',
            $report
        )
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    public function exportExcel(Request $request)
    {
        if (!$this->authorized()) {
            return redirect()->route('admin.login');
        }

        $report = $this->prepareReport($request);

        $spreadsheet = new Spreadsheet();

        /*
         * SHEET 1: REPORT SUMMARY
         */
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Report Summary');

        $summarySheet->setCellValue(
            'A1',
            'CITYPULSE'
        );

        $summarySheet->setCellValue(
            'A2',
            $report['selectedTitle']
        );

        $summarySheet->setCellValue(
            'A3',
            'Reporting Period'
        );

        $summarySheet->setCellValue(
            'B3',
            $report['fromDisplay'] .
            ' - ' .
            $report['toDisplay']
        );

        $summarySheet->setCellValue(
            'A4',
            'Timezone'
        );

        $summarySheet->setCellValue(
            'B4',
            'Philippine Standard Time (UTC+8)'
        );

        $summarySheet->setCellValue(
            'A5',
            'Metric'
        );

        $summarySheet->setCellValue(
            'B5',
            'Value'
        );

        $rowNumber = 6;

        foreach ($report['summary'] as $item) {
            $summarySheet->setCellValue(
                'A' . $rowNumber,
                $item['label']
            );

            $summarySheet->setCellValue(
                'B' . $rowNumber,
                $item['value']
            );

            $rowNumber++;
        }

        $summarySheet->getStyle('A1:B2')
            ->getFont()
            ->setBold(true);

        $summarySheet->getStyle('A5:B5')
            ->getFont()
            ->setBold(true);

        $summarySheet->getStyle('A5:B5')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('DDECE2');

        $summarySheet->getColumnDimension('A')
            ->setWidth(35);

        $summarySheet->getColumnDimension('B')
            ->setWidth(42);

        /*
         * Optional visual summary
         */
        if ($report['visualization']) {
            $rowNumber += 2;

            $summarySheet->setCellValue(
                'A' . $rowNumber,
                'Visual Summary'
            );

            $summarySheet->getStyle(
                'A' . $rowNumber
            )->getFont()->setBold(true);

            $rowNumber++;

            $maximum = max(
                1,
                ...array_column(
                    $report['summary'],
                    'value'
                )
            );

            foreach ($report['summary'] as $item) {
                $length = $item['value'] === 0
                    ? 0
                    : max(
                        1,
                        (int) round(
                            ($item['value'] / $maximum) * 25
                        )
                    );

                $summarySheet->setCellValue(
                    'A' . $rowNumber,
                    $item['label']
                );

                $summarySheet->setCellValue(
                    'B' . $rowNumber,
                    str_repeat('█', $length)
                );

                $summarySheet->getStyle(
                    'B' . $rowNumber
                )
                    ->getFont()
                    ->getColor()
                    ->setRGB('25824D');

                $rowNumber++;
            }
        }

        /*
         * SHEET 2: REPORT DETAILS
         */
        $detailSheet = $spreadsheet->createSheet();
        $detailSheet->setTitle('Report Details');

        $detailSheet->fromArray(
            $report['headers'],
            null,
            'A1'
        );

        /*
         * Export database values as explicit strings.
         * This also prevents user-supplied values
         * from being interpreted as Excel formulas.
         */
        if ($report['rows']) {
            foreach ($report['rows'] as $rowIndex => $values) {
                foreach ($values as $columnIndex => $value) {
                    $cell = Coordinate::stringFromColumnIndex(
                        $columnIndex + 1
                    ) . ($rowIndex + 2);

                    $detailSheet->setCellValueExplicit(
                        $cell,
                        (string) $value,
                        DataType::TYPE_STRING
                    );
                }
            }
        }

        $lastColumn = $detailSheet->getHighestColumn();

        $detailSheet->getStyle(
            'A1:' . $lastColumn . '1'
        )
            ->getFont()
            ->setBold(true);

        $detailSheet->getStyle(
            'A1:' . $lastColumn . '1'
        )
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('DDECE2');

        $detailSheet->freezePane('A2');

        $detailSheet->setAutoFilter(
            'A1:' .
            $lastColumn .
            max(2, $detailSheet->getHighestRow())
        );

        $columnCount = count($report['headers']);

        for ($columnIndex = 1; $columnIndex <= $columnCount; $columnIndex++) {
            $column = Coordinate::stringFromColumnIndex(
                $columnIndex
            );

            $detailSheet->getColumnDimension($column)
                ->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'citypulse-' .
            Str::slug($report['selectedTitle']) .
            '-' .
            now(self::REPORT_TIMEZONE)->format('Ymd-His') .
            '.xlsx';

        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);

                $writer->save('php://output');

                $spreadsheet->disconnectWorksheets();
            },
            $filename,
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }
}
