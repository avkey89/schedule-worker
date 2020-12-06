<?php

declare(strict_types=1);

namespace App\Application\Service\GoogleApi;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Exception;

class GoogleCalendar
{
    public static function getHoliday()
    {
        $httpClient = new Client();
        $holidays = [];

        try {
            $response =
                $httpClient->get('https://www.googleapis.com/calendar/v3/calendars/ru.russian%23holiday%40group.v.calendar.google.com/events?key=' .
                    $_ENV['GOOGLE_API_CALENDAR_KEY'])->getBody()->getContents();
            $holidaysList = json_decode($response);

            foreach ($holidaysList->items as $day) {
                $holidays[] = $day->start->date;
            }
        } catch (GuzzleException $e) {
        } catch (Exception $e) {
        }

        return $holidays;
    }
}