<?php

namespace App\Modules\Reports\Database\Factories;

use App\Modules\Branches\Models\Branch;
use App\Modules\Reports\Enums\ReportFormat;
use App\Modules\Reports\Enums\ReportStatus;
use App\Modules\Reports\Enums\ReportType;
use App\Modules\Reports\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        $reportType = $this->faker->randomElement(ReportType::cases());
        $status = $this->faker->randomElement(ReportStatus::cases());
        $format = $this->faker->randomElement(ReportFormat::cases());
        
        return [
            'report_type' => $reportType,
            'title' => $this->faker->sentence() . ' Report',
            'description' => $this->faker->optional()->paragraph(),
            'status' => $status,
            'format' => $format,
            'parameters' => $this->faker->optional()->randomElements([
                'start_date' => $this->faker->date(),
                'end_date' => $this->faker->date(),
                'branch_id' => $this->faker->randomNumber(),
                'customer_id' => $this->faker->randomNumber(),
                'include_details' => $this->faker->boolean(),
            ], $this->faker->numberBetween(0, 4)),
            'generated_by' => \App\Models\User::factory(),
            'branch_id' => Branch::factory(),
            'file_path' => $status === ReportStatus::Completed ? 'reports/' . $this->faker->uuid() . '.' . $format->fileExtension() : null,
            'file_size' => $status === ReportStatus::Completed ? $this->faker->numberBetween(1024, 10485760) : null,
            'record_count' => $status === ReportStatus::Completed ? $this->faker->numberBetween(10, 10000) : null,
            'generated_at' => $status === ReportStatus::Completed ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'expires_at' => $status === ReportStatus::Completed ? $this->faker->dateTimeBetween('+1 week', '+1 month') : null,
            'scheduled_at' => $status === ReportStatus::Scheduled ? $this->faker->dateTimeBetween('+1 hour', '+1 week') : null,
            'error_message' => $status === ReportStatus::Failed ? $this->faker->randomElement([
                'Database connection failed',
                'Memory limit exceeded',
                'File generation error',
                'Invalid parameters',
            ]) : null,
            'metadata' => $this->faker->optional()->randomElements([
                'generation_time' => $this->faker->numberBetween(1, 300),
                'source' => $this->faker->randomElement(['web', 'api', 'system']),
                'priority' => $this->faker->randomElement(['low', 'normal', 'high']),
            ], $this->faker->numberBetween(0, 3)),
        ];
    }

    public function transaction(): self
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => ReportType::Transaction,
            'title' => 'Transaction Report',
            'description' => 'Detailed transaction analysis',
        ]);
    }

    public function account(): self
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => ReportType::Account,
            'title' => 'Account Report',
            'description' => 'Account performance overview',
        ]);
    }

    public function revenue(): self
    {
        return $this->state(fn (array $attributes) => [
            'report_type' => ReportType::Revenue,
            'title' => 'Revenue Report',
            'description' => 'Financial revenue analysis',
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Completed,
            'generated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'file_path' => 'reports/' . $this->faker->uuid() . '.pdf',
            'file_size' => $this->faker->numberBetween(1024, 10485760),
            'record_count' => $this->faker->numberBetween(10, 10000),
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Failed,
            'error_message' => $this->faker->randomElement([
                'Database connection failed',
                'Memory limit exceeded',
                'File generation error',
            ]),
        ]);
    }

    public function scheduled(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ReportStatus::Scheduled,
            'scheduled_at' => $this->faker->dateTimeBetween('+1 hour', '+1 week'),
        ]);
    }

    public function pdf(): self
    {
        return $this->state(fn (array $attributes) => [
            'format' => ReportFormat::PDF,
        ]);
    }

    public function excel(): self
    {
        return $this->state(fn (array $attributes) => [
            'format' => ReportFormat::Excel,
        ]);
    }
}
