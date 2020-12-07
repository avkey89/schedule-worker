<?php

declare(strict_types=1);

namespace App\Application\Schedule\CommandHandler;

use App\Application\Service\GoogleApi\GoogleCalendar;
use App\Repository\Statics\EventCompanyRepository;
use App\Repository\Statics\WorkerRepository;

class ScheduleCommandHandler
{
    private WorkerRepository $workerRepository;
    private EventCompanyRepository $eventCompanyRepository;

    public function __construct(WorkerRepository $workerRepository, EventCompanyRepository $eventCompanyRepository)
    {
        $this->workerRepository = $workerRepository;
        $this->eventCompanyRepository = $eventCompanyRepository;
    }

    public function handler(int $userId, string $startDate, string $endDate, bool $offHour = false): array
    {
        $worker = $this->workerRepository->find($userId);
        $period = $this->generateFindPeriod($startDate, $endDate, $offHour);
        $vacationPeriodDate = $this->workerVacationPeriod($worker->getData()["vacation"]);
        $eventPeriods = $this->eventCompanyPeriod();

        if ($offHour == false) {
            $timeRanges = $this->defaultWorkerTimeRanges($worker->getData()["scheduleWorkDay"]);
            $workDays = array_diff($period, $vacationPeriodDate);
            $workDays = array_diff($workDays, GoogleCalendar::getHoliday());
            $schedule = $this->generateWorkerTimeRanges($workDays, $timeRanges, $eventPeriods);
        } else {
            $timeRanges = $this->defaultOffHourTimeRanges($worker->getData()["scheduleWorkDay"]);
            $workDays = $period;
            $schedule = $this->generateOffHourTimeRanges($workDays, $timeRanges, $eventPeriods, $vacationPeriodDate);
        }

        return $schedule ?? [];
    }

    private function generateWorkerTimeRanges(array $workDays, array $timeRanges, array $eventPeriods): array
    {
        $schedule = [];
        if (!empty($workDays)) {
            foreach($workDays as $day) {
                $item["day"] = $day;
                $timeRangesCurrent = $timeRanges;
                if (!empty($eventPeriods)) {
                    foreach ($eventPeriods as $eventPeriod) {
                        if ($eventPeriod["startDate"]->format('Y-m-d') == $day) {
                            foreach ($timeRanges as $index=>$timeRange) {
                                $timeRange['start'] = (new \DateTimeImmutable($day." ".$timeRange['start']))->format('Y-m-d H:i');
                                $timeRange['end'] = (new \DateTimeImmutable($day." ".$timeRange['end']))->format('Y-m-d H:i');

                                $eventStartDate = $eventPeriod['startDate']->format('Y-m-d H:i');
                                $eventEndDate = $eventPeriod['endDate']->format('Y-m-d H:i');

                                if ($timeRange['start'] == $eventStartDate && $timeRange['end'] >= $eventStartDate) {
                                    unset($timeRangesCurrent[$index]);
                                } elseif ($timeRange['start'] <= $eventStartDate && $timeRange['end'] >= $eventEndDate) {
                                    $timeRangesNew['start'] = $eventPeriod['endDate']->format('H:i');
                                    $timeRangesNew['end'] = $timeRangesCurrent[$index]['end'];
                                    $timeRangesCurrent[$index]['end'] = $eventPeriod['startDate']->format('H:i');

                                    array_push($timeRangesCurrent, $timeRangesNew);
                                } elseif ($timeRange['start'] <= $eventStartDate && $timeRange['end'] >= $eventStartDate) {
                                    $timeRangesCurrent[$index]['end'] = $eventPeriod['startDate']->format('H:i');
                                } elseif ($timeRange['start'] >= $eventStartDate && $timeRange['end'] >= $eventEndDate) {
                                    $timeRangesCurrent[$index]['start'] = $eventPeriod['endDate']->format('H:i');
                                } elseif ($timeRange['start'] >= $eventStartDate && $timeRange['end'] <= $eventEndDate) {
                                    unset($timeRangesCurrent[$index]);
                                }
                            }
                        }
                    }
                }

                $item["timeRanges"] = $timeRangesCurrent;

                $schedule[] = $item;
            }
        }

        return $schedule;
    }

    private function generateOffHourTimeRanges(array $workDays, array $timeRanges, array $eventPeriods, array $vacationPeriodDate): array
    {
        $schedule = [];
        if (!empty($workDays)) {
            foreach($workDays as $day) {
                $item["day"] = $day;
                if (
                    in_array($day, array_merge($vacationPeriodDate, GoogleCalendar::getHoliday()))
                    || in_array((new \DateTimeImmutable($day))->format("l"), ['Saturday', 'Sunday'])
                ) {
                    $timeRangesCurrent = [
                        0 => ['start'=>'00:00', 'end'=>'23:59']
                    ];
                } else {
                    $timeRangesCurrent = $timeRanges;
                }
                if (!empty($eventPeriods)) {
                    foreach ($eventPeriods as $eventPeriod) {
                        if ($eventPeriod["startDate"]->format('Y-m-d') == $day) {
                            foreach ($timeRanges as $index=>$timeRange) {
                                $timeRange['start'] = (new \DateTimeImmutable($day." ".$timeRange['start']))->format('Y-m-d H:i');
                                $timeRange['end'] = (new \DateTimeImmutable($day." ".$timeRange['end']))->format('Y-m-d H:i');

                                $eventStartDate = $eventPeriod['startDate']->format('Y-m-d H:i');
                                $eventEndDate = $eventPeriod['endDate']->format('Y-m-d H:i');

                                if ($timeRange['start'] >= $eventStartDate && $timeRange['end'] >= $eventEndDate) {
                                    $timeRangesCurrent[$index]['start'] = $eventPeriod['endDate']->format('H:i');
                                } elseif ($timeRange['start'] >= $eventStartDate && $timeRange['end'] <= $eventEndDate) {
                                    $timeRangesCurrent[$index]['start'] = $eventPeriod['startDate']->format('H:i');
                                    $timeRangesCurrent[$index]['end'] = $eventPeriod['endDate']->format('H:i');
                                }
                            }
                        }
                    }
                }

                $item["timeRanges"] = $timeRangesCurrent;

                $schedule[] = $item;
            }
        }

        return $schedule;
    }

    private function generateFindPeriod(string $startDate, string $endDate, bool $offHour = false): array
    {
        $period = [];
        $day = new \DateTimeImmutable($startDate);
        while($day >= new \DateTimeImmutable($startDate) && $day <= new \DateTimeImmutable($endDate)) {
            if ($offHour == true) {
                $period[] = $day->format("Y-m-d");
            } else {
                if (!in_array($day->format("l"), ['Saturday', 'Sunday'])) {
                    $period[] = $day->format("Y-m-d");
                }
            }

            $day = $day->modify("+1 day");
        }

        return $period;
    }

    private function defaultWorkerTimeRanges(array $scheduleWorkDay)
    {
        if (!empty($scheduleWorkDay["dinnerStart"])) {
            $timeRanges = [
                0 => [
                    'start' => $scheduleWorkDay["start"].':00',
                    'end' => $scheduleWorkDay["dinnerStart"].':00'
                ],
                1 => [
                    'start' => $scheduleWorkDay["dinnerEnd"].':00',
                    'end' => $scheduleWorkDay["end"].':00'
                ]
            ];
        } else {
            $timeRanges = [
                0 => [
                    'start' => $scheduleWorkDay["start"].':00',
                    'end' => $scheduleWorkDay["end"].':00'
                ]
            ];
        }

        return $timeRanges;
    }

    private function defaultOffHourTimeRanges(array $scheduleWorkDay)
    {
        if (!empty($scheduleWorkDay["dinnerStart"])) {
            $timeRanges = [
                0 => [
                    'start' => '00:00',
                    'end' => $scheduleWorkDay["start"].':00'
                ],
                1 => [
                    'start' => $scheduleWorkDay["dinnerStart"].':00',
                    'end' => $scheduleWorkDay["dinnerEnd"].':00'
                ],
                2 => [
                    'start' => $scheduleWorkDay["end"].':00',
                    'end' => '23:59'
                ]
            ];
        } else {
            $timeRanges = [
                0 => [
                    'start' => '00:00',
                    'end' => $scheduleWorkDay["start"].':00'
                ],
                1 => [
                    'start' => $scheduleWorkDay["end"].':00',
                    'end' => '23:59'
                ]
            ];
        }

        return $timeRanges;
    }

    private function workerVacationPeriod(?array $vacations)
    {
        $vacationPeriodDate = [];
        if (!empty($vacations)) {
            foreach($vacations as $vacation) {
                $periodVacation = $this->generateFindPeriod($vacation->getStart()->format('Y-m-d'), $vacation->getEnd()->format('Y-m-d'));
                $vacationPeriodDate = array_merge($vacationPeriodDate, $periodVacation);
            }
        }

        return $vacationPeriodDate;
    }

    private function eventCompanyPeriod()
    {
        $eventCompany = $this->eventCompanyRepository->all();
        if (!empty($eventCompany)) {
            foreach($eventCompany as $event) {
                $periodData = [];
                $periodData["startDate"] = $event->getStart();
                //$periodData["startTime"] = $event->getStart()->format('H');
                $periodData["endDate"] = $event->getEnd();
                //$periodData["endTime"] = $event->getEnd()->format('H');

                $eventPeriods[] = $periodData;
            }
        }

        return $eventPeriods ?? [];
    }
}