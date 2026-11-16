<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard numbers in ONE database round trip (instead of 5-6), cached briefly.
 * Each query to a remote Postgres costs a network hop, so fewer queries = faster page.
 */
class DashboardStats
{
    public static function staff(bool $withRevenue): array
    {
        return Cache::remember('dash.staff.'.(int) $withRevenue, 45, function () use ($withRevenue) {
            $tz = config('clinic.timezone');
            $from = now($tz)->startOfDay()->utc();
            $to = now($tz)->endOfDay()->utc();
            $mFrom = now($tz)->startOfMonth()->utc();
            $mTo = now($tz)->endOfMonth()->utc();

            $row = DB::selectOne(
                "select
                    (select count(*) from patients where deleted_at is null) as patients,
                    (select count(*) from doctors where deleted_at is null) as doctors,
                    (select count(*) from appointments where start_at between ? and ? and status <> 'cancelled') as today,
                    (select count(*) from appointments where status = 'pending') as pending,
                    (select coalesce(sum(s.price), 0) from appointments a join services s on s.id = a.service_id
                        where a.status = 'completed' and a.start_at between ? and ?) as revenue",
                [$from, $to, $mFrom, $mTo]
            );

            return [
                'patients' => (int) $row->patients,
                'doctors' => (int) $row->doctors,
                'today' => (int) $row->today,
                'pending' => (int) $row->pending,
                'revenue' => $withRevenue ? (float) $row->revenue : null,
            ];
        });
    }

    public static function patient(int $patientId): array
    {
        return Cache::remember('dash.patient.'.$patientId, 20, function () use ($patientId) {
            $row = DB::selectOne(
                "select
                    count(*) filter (where status in ('pending','confirmed') and start_at > now()) as upcoming,
                    count(*) filter (where status = 'completed') as completed
                 from appointments where patient_id = ?",
                [$patientId]
            );

            return ['upcoming' => (int) $row->upcoming, 'completed' => (int) $row->completed];
        });
    }
}
