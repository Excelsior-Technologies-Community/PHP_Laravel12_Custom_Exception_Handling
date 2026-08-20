<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExceptionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'url',
        'exception_type',
        'severity',
        'status',
        'stack_trace',
        'ip_address',
        'user_agent',
        'http_method',
        'request_payload',
    ];

    protected $casts = [
        'request_payload' => 'array',
    ];

    public const SEVERITIES = ['low', 'medium', 'high', 'critical'];
    public const STATUSES   = ['open', 'investigating', 'resolved'];

    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'low'      => 'success',
            'medium'   => 'warning',
            'high'     => 'danger',
            'critical' => 'dark',
            default    => 'secondary',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open'          => 'danger',
            'investigating' => 'warning',
            'resolved'      => 'success',
            default         => 'secondary',
        };
    }
}
