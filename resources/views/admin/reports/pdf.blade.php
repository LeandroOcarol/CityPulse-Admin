
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>CityPulse - {{ $selectedTitle }}</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 25px 30px 48px 30px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #25352d;
            margin: 0;
            padding: 0;
        }

        /* HEADER */

        .report-header {
            margin-bottom: 13px;
        }

        h1 {
            color: #17663a;
            font-size: 21px;
            margin: 0 0 3px 0;
        }

        .subtitle {
            color: #65766c;
            font-size: 9px;
            margin: 0;
        }

        /* REPORT INFORMATION */

        .report-info {
            margin-top: 12px;
            margin-bottom: 15px;
            border-bottom: 2px solid #25824d;
            padding-bottom: 9px;
            line-height: 1.5;
        }

        .report-info-row {
            margin-bottom: 2px;
        }

        .report-info strong {
            color: #25352d;
        }

        /* SECTION TITLES */

        h2 {
            color: #17663a;
            font-size: 12px;
            margin: 15px 0 9px 0;
            page-break-after: avoid;
        }

        /* SUMMARY CARDS */

        .summary-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin: 0 0 12px 0;
            table-layout: fixed;
        }

        .summary-cell {
            background: #eaf4ed;
            padding: 10px;
            border-radius: 4px;
            vertical-align: top;
        }

        .summary-label {
            color: #536a5a;
            font-size: 9px;
            line-height: 1.4;
        }

        .summary-value {
            font-size: 18px;
            color: #17663a;
            font-weight: bold;
            margin-top: 4px;
        }

        /* VISUAL SUMMARY */

        .visual-summary {
            margin-bottom: 12px;
        }

        .visual-item {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .visual-label {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .bar-track {
            background: #e5e7eb;
            height: 9px;
            width: 100%;
        }

        .bar-fill {
            background: #25824d;
            height: 9px;
        }

        /* DETAILED RECORDS */

        .details-count {
            margin: 0 0 8px 0;
            font-size: 9px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 8px;
        }

        .details-table th {
            background: #17663a;
            color: #ffffff;
            padding: 7px 6px;
            text-align: left;
            vertical-align: middle;
            font-weight: bold;
        }

        .details-table td {
            border-bottom: 1px solid #dce5de;
            padding: 6px;
            vertical-align: top;
            overflow-wrap: break-word;
            word-wrap: break-word;
            line-height: 1.4;
        }

        .details-table tbody tr:nth-child(even) {
            background: #f3f7f4;
        }

        .details-table thead {
            display: table-header-group;
        }

        .details-table tbody tr {
            page-break-inside: avoid;
        }

        /* FOOTER */

        .footer {
            position: fixed;
            bottom: -34px;
            left: 0;
            right: 0;

            border-top: 1px solid #dce5de;
            padding-top: 5px;

            font-size: 7px;
            color: #76847a;
            line-height: 1.5;
        }

        .footer-title {
            color: #17663a;
            font-weight: bold;
        }

        .footer-note {
            margin-top: 2px;
        }
    </style>
</head>

<body>

    <!-- FIXED FOOTER -->

    <div class="footer">

        <div class="footer-title">
            CityPulse — Cebu City Tourism and Safety Reporting
        </div>

        <div class="footer-note">
            Philippine Standard Time (UTC+8).
            Generated for authorized administrative use.
            Historical safety data does not guarantee future conditions.
        </div>

    </div>


    <!-- REPORT HEADER -->

    <div class="report-header">

        <h1>CITYPULSE</h1>

        <p class="subtitle">
            A Geofenced Smart Tourism System
            with Predictive Safety Analytics
        </p>

    </div>


    <!-- REPORT INFORMATION -->

    <div class="report-info">

        <div class="report-info-row">
            <strong>Report:</strong>
            {{ $selectedTitle }}
        </div>

        <div class="report-info-row">
            <strong>Reporting Period:</strong>
            {{ $fromDisplay }} – {{ $toDisplay }}
        </div>

        <div class="report-info-row">
            <strong>Period Type:</strong>
            {{ $periodLabel }}
        </div>

        <div class="report-info-row">
            <strong>Timezone:</strong>
            Philippine Standard Time (UTC+8)
        </div>

        <div class="report-info-row">
            <strong>Generated:</strong>
            {{ now('Asia/Manila')->format('F d, Y h:i A') }}
            PST
        </div>

    </div>


    <!-- REPORT SUMMARY -->

    <h2>Report Summary</h2>

    <table class="summary-table">

        <tr>

            @foreach ($summary as $item)

                <td class="summary-cell">

                    <div class="summary-label">
                        {{ $item['label'] }}
                    </div>

                    <div class="summary-value">
                        {{ number_format($item['value']) }}
                    </div>

                </td>

            @endforeach

        </tr>

    </table>


    <!-- VISUAL SUMMARY -->

    @if ($visualization)

        @php
            $maximum = max(
                1,
                ...array_column($summary, 'value')
            );
        @endphp

        <div class="visual-summary">

            <h2>Visual Summary</h2>

            @foreach ($summary as $item)

                @php
                    $percentage = $maximum > 0
                        ? ($item['value'] / $maximum) * 100
                        : 0;
                @endphp

                <div class="visual-item">

                    <div class="visual-label">
                        {{ $item['label'] }}:
                        {{ number_format($item['value']) }}
                    </div>

                    <div class="bar-track">

                        <div
                            class="bar-fill"
                            style="width: {{ $percentage }}%;"
                        ></div>

                    </div>

                </div>

            @endforeach

        </div>

    @endif


    <!-- DETAILED RECORDS -->

    <h2>Detailed Records</h2>

    <p class="details-count">
        Total matching records:
        <strong>{{ number_format($recordCount) }}</strong>
    </p>

    <table class="details-table">

        <thead>

            <tr>

                @foreach ($headers as $header)

                    <th>
                        {{ $header }}
                    </th>

                @endforeach

            </tr>

        </thead>

        <tbody>

            @forelse ($rows as $row)

                <tr>

                    @foreach ($row as $value)

                        <td>
                            {{ $value }}
                        </td>

                    @endforeach

                </tr>

            @empty

                <tr>

                    <td colspan="{{ count($headers) }}">
                        No records were found
                        for the selected reporting period.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</body>
</html>
