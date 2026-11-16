<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class PredictiveAnalyticsController extends Controller
{
    private const MODEL_DIRECTORY = 'app/private/predictive-analytics';

    public function index()
    {
        if (!session()->has('admin_user')) {
            return redirect()->route('admin.login');
        }

        $directory = storage_path(self::MODEL_DIRECTORY);

        /*
        |--------------------------------------------------------------------------
        | 1. Load Random Forest Metadata
        |--------------------------------------------------------------------------
        */

        $metadataPath = $directory .
            '/citypulse_random_forest_metadata.json';

        $metadata = [];
        $metadataLoaded = false;

        if (is_file($metadataPath) && is_readable($metadataPath)) {
            $contents = file_get_contents($metadataPath);

            if ($contents !== false) {
                $decoded = json_decode($contents, true);

                if (
                    json_last_error() === JSON_ERROR_NONE &&
                    is_array($decoded)
                ) {
                    $metadata = $decoded;
                    $metadataLoaded = true;
                }
            }
        }

        $modelName = $metadata['model_name'] ?? null;
        $modelType = $metadata['model_type'] ?? null;

        $trainingRows = $metadata['training_rows'] ?? null;
        $testRows = $metadata['test_rows'] ?? null;
        $testTargetMonth = $metadata['test_target_month'] ?? null;

        $features = $metadata['features'] ?? [];
        $riskLabelRule = $metadata['risk_label_rule'] ?? [];
        $severityWeighting = $metadata['severity_weighting'] ?? [];
        $modelNotes = $metadata['notes'] ?? [];

        $accuracy = isset($metadata['accuracy']) &&
            is_numeric($metadata['accuracy'])
                ? (float) $metadata['accuracy']
                : null;

        $accuracyPercentage = $accuracy !== null
            ? round($accuracy * 100, 2)
            : null;

        /*
        |--------------------------------------------------------------------------
        | 2. Load Random Forest Evaluation Results
        |--------------------------------------------------------------------------
        */

        $evaluationPath = $directory .
            '/CityPulse_random_forest_evaluation.csv';

        $evaluationRows = $this->readCsv($evaluationPath);

        $evaluation = [];

        foreach ($evaluationRows as $row) {
            $class = trim((string) ($row['class'] ?? ''));

            if ($class === '') {
                continue;
            }

            $evaluation[] = [
                'class' => $class,
                'precision' => $this->toNumber(
                    $row['precision'] ?? null
                ),
                'recall' => $this->toNumber(
                    $row['recall'] ?? null
                ),
                'f1_score' => $this->toNumber(
                    $row['f1_score'] ?? null
                ),
                'support' => $this->toNumber(
                    $row['support'] ?? null
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Load Confusion Matrix
        |--------------------------------------------------------------------------
        */

        $confusionPath = $directory .
            '/CityPulse_random_forest_confusion_matrix.csv';

        $confusionRows = $this->readCsv($confusionPath);

        $confusionMatrix = [];

        foreach ($confusionRows as $row) {
            $actual = trim(
                (string) ($row['actual_class'] ?? '')
            );

            if ($actual === '') {
                continue;
            }

            // Normalize labels such as actual_High to High.
            $actual = preg_replace('/^actual_/i', '', $actual);

            $confusionMatrix[] = [
                'actual_class' => $actual,
                'predicted_Low' => (int) (
                    $row['predicted_Low'] ?? 0
                ),
                'predicted_Medium' => (int) (
                    $row['predicted_Medium'] ?? 0
                ),
                'predicted_High' => (int) (
                    $row['predicted_High'] ?? 0
                ),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Load July 2026 Barangay Risk Predictions
        |--------------------------------------------------------------------------
        */

        $predictionPath = $directory .
            '/CityPulse_July_2026_barangay_risk_predictions.csv';

        $predictionRows = $this->readCsv($predictionPath);

        $predictions = [];

        $riskDistribution = [
            'Low' => 0,
            'Medium' => 0,
            'High' => 0,
        ];

        $predictionMonths = [];
        $totalConfidence = 0;
        $confidenceCount = 0;

        foreach ($predictionRows as $row) {
            $barangay = trim(
                (string) ($row['barangay'] ?? '')
            );

            $riskLevel = ucfirst(strtolower(trim(
                (string) ($row['predicted_risk_level'] ?? '')
            )));

            $predictionMonth = trim(
                (string) ($row['prediction_for_month'] ?? '')
            );

            if (
                $barangay === '' ||
                !array_key_exists($riskLevel, $riskDistribution) ||
                !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $predictionMonth)
            ) {
                continue;
            }

            // This recovered export is specifically for July 2026.
            if ($predictionMonth !== '2026-07') {
                continue;
            }

            $confidence = $this->toNumber(
                $row['confidence'] ?? null
            );

            $probHigh = $this->toNumber(
                $row['prob_high'] ?? null
            );

            $probMedium = $this->toNumber(
                $row['prob_medium'] ?? null
            );

            $probLow = $this->toNumber(
                $row['prob_low'] ?? null
            );

            $predictions[] = [
                'barangay' => $barangay,
                'prediction_for_month' => $predictionMonth,
                'risk_level' => $riskLevel,
                'confidence' => $confidence,
                'confidence_percentage' => $confidence !== null
                    ? round($confidence * 100, 2)
                    : null,

                'prob_high' => $probHigh,
                'prob_medium' => $probMedium,
                'prob_low' => $probLow,

                'current_total_crimes' => (int) (
                    $row['current_total_crimes'] ?? 0
                ),

                'current_high_crimes' => (int) (
                    $row['current_high_crimes'] ?? 0
                ),

                'current_medium_crimes' => (int) (
                    $row['current_medium_crimes'] ?? 0
                ),

                'current_low_crimes' => (int) (
                    $row['current_low_crimes'] ?? 0
                ),

                'current_weighted_score' => $this->toNumber(
                    $row['current_weighted_score'] ?? null
                ),

                'rolling_3m_total_crimes' => $this->toNumber(
                    $row['rolling_3m_total_crimes'] ?? null
                ),

                'rolling_3m_weighted_score' => $this->toNumber(
                    $row['rolling_3m_weighted_score'] ?? null
                ),
            ];

            $riskDistribution[$riskLevel]++;

            $predictionMonths[$predictionMonth] = true;

            if ($confidence !== null) {
                $totalConfidence += $confidence;
                $confidenceCount++;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Prediction Summary
        |--------------------------------------------------------------------------
        */

        $predictedBarangays = count($predictions);

        $predictionMonth = count($predictionMonths) === 1
            ? array_key_first($predictionMonths)
            : null;

        $averageConfidence = $confidenceCount > 0
            ? round(
                ($totalConfidence / $confidenceCount) * 100,
                2
            )
            : null;

        $riskPercentages = [];

        foreach ($riskDistribution as $level => $count) {
            $riskPercentages[$level] = $predictedBarangays > 0
                ? round(($count / $predictedBarangays) * 100, 2)
                : 0;
        }

        /*
        |--------------------------------------------------------------------------
        | 6. Sort Predictions
        |--------------------------------------------------------------------------
        |
        | High-risk predictions appear first,
        | followed by Medium and Low.
        | Within a risk level, higher model
        | confidence appears first.
        |
        */

        $riskOrder = [
            'High' => 0,
            'Medium' => 1,
            'Low' => 2,
        ];

        usort($predictions, function ($a, $b) use ($riskOrder) {
            $riskComparison =
                $riskOrder[$a['risk_level']] <=>
                $riskOrder[$b['risk_level']];

            if ($riskComparison !== 0) {
                return $riskComparison;
            }

            return ($b['confidence'] ?? -1)
                <=> ($a['confidence'] ?? -1);
        });

        /*
        |--------------------------------------------------------------------------
        | 7. File Availability
        |--------------------------------------------------------------------------
        */

        $fileStatus = [
            'metadata' => $metadataLoaded,

            'evaluation' => is_file($evaluationPath)
                && is_readable($evaluationPath)
                && count($evaluation) > 0,

            'confusion_matrix' => is_file($confusionPath)
                && is_readable($confusionPath)
                && count($confusionMatrix) > 0,

            'predictions' => is_file($predictionPath)
                && is_readable($predictionPath)
                && $predictedBarangays > 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | 8. Return Predictive Analytics Dashboard
        |--------------------------------------------------------------------------
        */

        return view('admin.predictive-analytics.index', [
            'metadata' => $metadata,

            'modelName' => $modelName,
            'modelType' => $modelType,

            'trainingRows' => $trainingRows,
            'testRows' => $testRows,
            'testTargetMonth' => $testTargetMonth,

            'accuracy' => $accuracy,
            'accuracyPercentage' => $accuracyPercentage,

            'features' => $features,
            'riskLabelRule' => $riskLabelRule,
            'severityWeighting' => $severityWeighting,
            'modelNotes' => $modelNotes,

            'evaluation' => $evaluation,
            'confusionMatrix' => $confusionMatrix,

            'predictions' => $predictions,
            'predictedBarangays' => $predictedBarangays,
            'predictionMonth' => $predictionMonth,
            'riskDistribution' => $riskDistribution,
            'riskPercentages' => $riskPercentages,
            'averageConfidence' => $averageConfidence,

            'fileStatus' => $fileStatus,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Read CSV File
    |--------------------------------------------------------------------------
    */

    private function readCsv(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            return [];
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            return [];
        }

        try {
            $headers = fgetcsv($handle);

            if ($headers === false) {
                return [];
            }

            $headers = array_map(
                function ($header, $index) {
                    $header = trim(
                        (string) $header,
                        "\xEF\xBB\xBF \t\n\r\0\x0B"
                    );

                    if ($index === 0 && $header === '') {
                        return 'actual_class';
                    }

                    return $header;
                },
                $headers,
                array_keys($headers)
            );

            $results = [];

            while (($values = fgetcsv($handle)) !== false) {
                if (count($values) !== count($headers)) {
                    continue;
                }

                $row = array_combine($headers, $values);

                if ($row !== false) {
                    $results[] = $row;
                }
            }

            return $results;

        } catch (\Throwable $exception) {
            Log::warning(
                'Predictive Analytics CSV reading failed.',
                [
                    'file' => basename($path),
                    'message' => $exception->getMessage(),
                ]
            );

            return [];

        } finally {
            fclose($handle);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Convert CSV Numeric Values
    |--------------------------------------------------------------------------
    */

    private function toNumber($value): ?float
    {
        if (
            $value === null ||
            $value === '' ||
            !is_numeric($value)
        ) {
            return null;
        }

        $number = (float) $value;

        return is_finite($number) ? $number : null;
    }
}