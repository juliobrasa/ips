<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class LogAuditTrail
{
    public function handle(object $event): void
    {
        $eventClass = get_class($event);
        $eventName = class_basename($eventClass);

        $data = $this->extractEventData($event);

        try {
            AuditLog::create([
                'event_type' => $eventName,
                'event_class' => $eventClass,
                'model_type' => $data['model_type'] ?? null,
                'model_id' => $data['model_id'] ?? null,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'data' => $data['extra'] ?? [],
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to log audit trail for {$eventName}: " . $e->getMessage());
        }
    }

    protected function extractEventData(object $event): array
    {
        $data = [
            'model_type' => null,
            'model_id' => null,
            'user_id' => null,
            'extra' => [],
        ];

        // Extract model information from common event properties
        $reflection = new \ReflectionClass($event);

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($event);
            $name = $property->getName();

            if (is_object($value) && method_exists($value, 'getKey')) {
                $data['model_type'] = get_class($value);
                $data['model_id'] = $value->getKey();
            } elseif ($name === 'initiatedBy' || $name === 'userId') {
                $data['user_id'] = $value;
            } elseif (is_scalar($value) || is_array($value)) {
                $data['extra'][$name] = $value;
            }
        }

        return $data;
    }
}
