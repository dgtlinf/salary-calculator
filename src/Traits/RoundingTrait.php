<?php

namespace Dgtlinf\SalaryCalculator\Traits;

trait RoundingTrait
{
    /**
     * Round value using global precision from config (default: 2).
     *
     * Uses standard mathematical rounding (PHP_ROUND_HALF_UP),
     * compliant with EU accounting standards.
     *
     * @param  float|int  $value
     * @param  int|null   $precision
     * @return float
     */
    protected function round2(float|int $value, ?int $precision = null): float
    {
        // Retrieve precision from config if not provided
        $precision ??= (int) config('salary-calculator.rounding_precision', 2);

        return round($value, $precision, PHP_ROUND_HALF_UP);
    }

    /**
     * Recursively rounds all numeric values in an array using round2().
     *
     * @param  array  $data
     * @return array
     */
    public function roundArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->roundArray($value);
            } elseif (is_numeric($value)) {
                $data[$key] = $this->round2((float) $value);
            }
        }

        return $data;
    }
}
