<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
    public function fromCsvRow(array $r): static
    {
        $norm = fn($v) => is_string($v) ? trim($v) : $v;

        $toInt = function ($v) {
            if ($v === '' || $v === null) return null;
            return is_numeric($v) ? (int)$v : null;
        };

        $nullIfEmpty = function ($v) use ($norm) {
            $v = $norm($v);
            return ($v === '' || $v === null) ? null : $v;
        };

        return $this->state([
            'location_code'        => $toInt($r['location_code'] ?? null),
            'location_name'        => (string) $norm($r['location_name'] ?? ''),
            'location_code_parent' => $toInt($r['location_code_parent'] ?? null),
            'country_iso_code'     => $nullIfEmpty($r['country_iso_code'] ?? null),
            'location_type'        => $nullIfEmpty($r['location_type'] ?? null),
            'available_sources'    => $nullIfEmpty($r['available_sources'] ?? null),
            'language_name'        => $nullIfEmpty($r['language_name'] ?? null),
            'language_code'        => (string) $norm($r['language_code'] ?? ''),
            'keywords'             => $toInt($r['keywords'] ?? null),
            'serps'                => $toInt($r['serps'] ?? null),
        ]);
    }
}
