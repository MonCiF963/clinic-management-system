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
                    (select count(*) from appointments where deleted_at is null and start_at between ? and ? and status <> 'cancelled') as today,
                    (select count(*) from appointments where deleted_at is null and status = 'pending') as pending,
                    (select coalesce(sum(s.price), 0) from appointments a join services s on s.id = a.service_id
                        where a.deleted_at is null and a.status = 'completed' and a.start_at between ? and ?) as revenue",
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
                 from appointments where deleted_at is null and patient_id = ?",
                [$patientId]
            );

            return ['upcoming' => (int) $row->upcoming, 'completed' => (int) $row->completed];
        });
    }

    /** Everything the admin dashboard shows, in ONE query, cached briefly. */
    public static function admin(): array
    {
        return Cache::remember('dash.admin', 45, function () {
            $tz = config('clinic.timezone');
            $from = now($tz)->startOfDay()->utc();
            $to = now($tz)->endOfDay()->utc();
            $mFrom = now($tz)->startOfMonth()->utc();
            $mTo = now($tz)->endOfMonth()->utc();
            $d0 = now($tz)->subDays(6)->toDateString();
            $d1 = now($tz)->addDays(7)->toDateString();

            $r = DB::selectOne(
                "select
                  (select count(*) from patients where deleted_at is null) as patients,
                  (select count(*) from doctors where deleted_at is null) as doctors,
                  (select count(*) from users where deleted_at is null) as users,
                  (select count(*) from appointments where deleted_at is null and start_at between ? and ? and status <> 'cancelled') as today,
                  (select count(*) from appointments where deleted_at is null and status = 'pending') as pending,
                  (select coalesce(sum(s.price), 0) from appointments a join services s on s.id = a.service_id
                      where a.deleted_at is null and a.status = 'completed' and a.start_at between ? and ?) as revenue,
                  (select coalesce(json_agg(x), '[]'::json) from (select status, count(*)::int as c from appointments where deleted_at is null group by status) x) as by_status,
                  (select coalesce(json_agg(x order by x.d), '[]'::json) from (
                      select to_char(g::date, 'YYYY-MM-DD') as d,
                        (select count(*)::int from appointments a where a.deleted_at is null and a.status <> 'cancelled'
                           and (a.start_at at time zone cast(? as text))::date = g::date) as c
                      from generate_series(cast(? as date), cast(? as date), interval '1 day') g) x) as daily,
                  (select coalesce(json_agg(x), '[]'::json) from (
                      select u.name, count(*)::int as c from appointments a join doctors d on d.id = a.doctor_id join users u on u.id = d.user_id
                      where a.deleted_at is null group by u.name order by c desc limit 5) x) as top_doctors,
                  (select coalesce(json_agg(x), '[]'::json) from (
                      select s.name, count(*)::int as c from appointments a join services s on s.id = a.service_id
                      where a.deleted_at is null group by s.name order by c desc limit 5) x) as top_services,
                  (select coalesce(json_agg(x), '[]'::json) from (
                      select a.start_at, a.status, pu.name as patient, du.name as doctor, s.name as service
                      from appointments a join patients p on p.id = a.patient_id join users pu on pu.id = p.user_id
                      join doctors d on d.id = a.doctor_id join users du on du.id = d.user_id join services s on s.id = a.service_id
                      where a.deleted_at is null order by a.created_at desc limit 6) x) as recent",
                [$from, $to, $mFrom, $mTo, $tz, $d0, $d1]
            );

            $daily = json_decode($r->daily, true) ?: [];
            $week = array_sum(array_column(array_slice($daily, 7, 7), 'c'));
            $prev = array_sum(array_column(array_slice($daily, 0, 7), 'c'));

            return [
                'patients' => (int) $r->patients,
                'doctors' => (int) $r->doctors,
                'users' => (int) $r->users,
                'today' => (int) $r->today,
                'pending' => (int) $r->pending,
                'revenue' => (float) $r->revenue,
                'by_status' => json_decode($r->by_status, true) ?: [],
                'daily' => $daily,
                'top_doctors' => json_decode($r->top_doctors, true) ?: [],
                'top_services' => json_decode($r->top_services, true) ?: [],
                'recent' => json_decode($r->recent, true) ?: [],
                'week' => $week,
                'prev' => $prev,
            ];
        });
    }

    /** Call after any appointment change so the dashboard is not stale. */
    public static function flush(): void
    {
        foreach (['dash.admin', 'dash.staff.0', 'dash.staff.1'] as $k) {
            Cache::forget($k);
        }
    }
}
