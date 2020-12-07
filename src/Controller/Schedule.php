<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Schedule\Command\ScheduleCommand;
use App\Application\Schedule\CommandHandler\ScheduleCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Schedule extends AbstractController
{
    /**
     * @Route("/schedule-worker", name="schedule_worker", methods={"GET"})
     */
    public function scheduleByWorker(Request $request, ScheduleCommandHandler $scheduleCommandHandler, ValidatorInterface $validator): JsonResponse
    {
        $data = ["success"=>true];
        $status = JsonResponse::HTTP_OK;
        try {
            $scheduleCommand = new ScheduleCommand((int)$request->query->get('userId'), $request->query->get('startDate'), $request->query->get('endDate'));
            $errors = $validator->validate($scheduleCommand);
            if (count($errors) > 0) {
                $data["success"] = false;
                foreach($errors as $error) {
                    $data["error"][] = $error->getMessage();
                }
            } else {
                $data["schedule"] = $scheduleCommandHandler->handler($scheduleCommand->userId, $scheduleCommand->startDate, $scheduleCommand->endDate);
            }
        } catch (\DomainException $exception) {
            $data["success"] = false;
            $data["error"] = $exception->getMessage();
            $status = JsonResponse::HTTP_BAD_REQUEST;
        } catch (\Exception $exception) {
            $data["success"] = false;
            $data["error"] = $exception->getMessage();
            $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $this->json($data, $status);
    }

    /**
     * @Route("/schedule-off-hour", name="schedule_off_hour", methods={"GET"})
     */
    public function scheduleOffHourByWorker(Request $request, ScheduleCommandHandler $scheduleCommandHandler, ValidatorInterface $validator): JsonResponse
    {
        $data = ["success"=>true];
        $status = JsonResponse::HTTP_OK;
        try {
            $scheduleCommand = new ScheduleCommand((int)$request->query->get('userId'), $request->query->get('startDate'), $request->query->get('endDate'));
            $errors = $validator->validate($scheduleCommand);
            if (count($errors) > 0) {
                $data["success"] = false;
                foreach($errors as $error) {
                    $data["error"][] = $error->getMessage();
                }
            } else {
                $data["schedule"] = $scheduleCommandHandler->handler($scheduleCommand->userId, $scheduleCommand->startDate, $scheduleCommand->endDate, true);
            }
        } catch (\DomainException $exception) {
            $data["success"] = false;
            $data["error"] = $exception->getMessage();
            $status = JsonResponse::HTTP_BAD_REQUEST;
        } catch (\Exception $exception) {
            $data["success"] = false;
            $data["error"] = $exception->getMessage();
            $status = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $this->json($data, $status);
    }
}