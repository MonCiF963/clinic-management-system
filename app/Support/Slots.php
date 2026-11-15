<?php

namespace App\Support;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\TimeOff;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Slots
{
    public static function tz(): string
    {
        return config('clinic.timezone');
    }

    /**
     * Free start times (clinic timezone) for a doctor, service and date.
     *
     * @return Collection<int, Carbon>
     */
    public static function for(Doctor $doctor, Service $service, string $date): Collection
    {
        $tz = self::tz();
        $day = Carbon::createFromFormat('Y-m-d', $date, $tz)->startOfDay();
        $duration = max(5, (int) $service->duration_minutes);
        $earliest = now($tz)->addMinutes((int) config('clinic.min_notice_minutes'));

        $dayStart = $day->copy()->utc();
        $dayEnd = $day->copy()->endOfDay()->utc();

        $busy = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->where('start_at', '<', $dayEnd)
            ->where('end_at', '>', $dayStart)
            ->get(['start_at', 'end_at']);

        $off = TimeOff::query()
            ->where('doctor_id', $doctor->id)
            ->where('start_at', '<', $dayEnd)
            ->where('end_at', '>', $dayStart)
            ->get(['start_at', 'end_at']);

        $blocked = $busy->concat($off);

        $slots = collect();

        $hours = $doctor->availabilities()
            ->where('weekday', $day->dayOfWeek) // 0 = Sunday ... 6 = Saturday
            ->orderBy('start_time')
            ->get();

        foreach ($hours as $range) {
            [$sh, $sm] = array_map('intval', explode(':', $range->start_time));
            [$eh, $em] = array_map('intval', explode(':', $range->end_time));

            $cursor = $day->copy()->setTime($sh, $sm);
            $end = $day->copy()->setTime($eh, $em);

            while ($cursor->copy()->addMinutes($duration)->lte($end)) {
                $start = $cursor->copy();
                $finish = $start->copy()->addMinutes($duration);

                $taken = $blocked->contains(
                    fn ($b) => $b->start_at->lt($finish) && $b->end_at->gt($start)
                );

                if ($start->gte($earliest) && ! $taken) {
                    $slots->push($start);
                }

                $cursor->addMinutes($duration);
            }
        }

        return $slots->values();
    }
}
