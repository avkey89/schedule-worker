<?php

declare(strict_types=1);

namespace App\Application\Service\GoogleApi;

use GuzzleHttp\Client;

class GoogleCalendar
{
    public static function getHoliday()
    {
        $httpClient = new Client();
        $response = $httpClient->get('https://www.googleapis.com/calendar/v3/calendars/ru.russian%23holiday%40group.v.calendar.google.com/events?key='.$_ENV['GOOGLE_API_CALENDAR_KEY'])->getBody()->getContents();

        $holidays = [];
        $holidaysList = json_decode($response)->items;
        foreach ($holidaysList as $day) {
            $holidays[] = $day->start->date;
        }

        return $holidays;
    }
}