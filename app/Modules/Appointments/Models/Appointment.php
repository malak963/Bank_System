<?php

namespace App\Modules\Appointments\Models;

use App\Modules\Appointments\Enums\AppointmentStatus;
use App\Modules\Appointments\Enums\ServiceType;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $table = 'appointments';

    protected $fillable = [
        'branch_id',
        'customer_id',
        'service_type',
        'status',
        'appointment_date',
        'appointment_time',
        'estimated_duration',
        'notes',
        'confirmation_sent_at',
        'reminder_sent_at',
        'checked_in_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'service_type' => ServiceType::class,
            'status' => AppointmentStatus::class,
            'appointment_date' => 'date',
            'appointment_time' => 'datetime',
            'estimated_duration' => 'integer',
            'confirmation_sent_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isConfirmed(): bool
    {
        return $this->status === AppointmentStatus::Confirmed;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [AppointmentStatus::Pending, AppointmentStatus::Confirmed]);
    }

    protected static function newFactory(): \App\Modules\Appointments\Database\Factories\AppointmentFactory
    {
        return \App\Modules\Appointments\Database\Factories\AppointmentFactory::new();
    }
}
