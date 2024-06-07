<?php

namespace Controllers;

require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../repositories/CalendarCampaingRepository.php';
require_once __DIR__ . '/../models/CalendarCampaing.php';
require_once __DIR__ . '/../models/CalendarEvent.php';


use Controllers\BaseController;
use Repositories\CalendarCampaingRepository;
use Models\CalendarCampaing;
use Models\CalendarEvent;
use Exception;


class CalendarCampaingController extends BaseController
{
  private static $ACTION_INIT_CALENDAR = "INIT_CALENDAR";

  public function __construct()
  {
  }

  public function initCalendar($campaings) {
    $result_formatted = array();

    foreach ($campaings as $iter) {
      $calendarEvent = new CalendarEvent((object) $iter);
      $result_formatted[] = $calendarEvent;
    }

    $calendar = new CalendarCampaing($result_formatted);

    return $calendar;
  }

  public function handlePostRequest()
  {

    // Obtiene la data del request
    $data = $this->getRequestData();
    //Ver el formato en el que llegan los datos

    // Obtiene y verifica la acción de la solicitud
    $action = $data['action'] ? $data['action'] : null;

    if (is_null($action)) {
      $this->sendResponse(false, "No action specified");
      return;
    }

    // Verifica que la solicitud sea de tipo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $this->sendResponse(false, "ERROR is not a POST request " . $action);
      return;
    }

    switch ($action) {

      case self::$ACTION_INIT_CALENDAR:
        $result = CalendarCampaingRepository::getAllCampaingEvents();

        $calendar = $this->initCalendar($result);
        
        $this->sendResponse(true, $calendar);
        break;

      /*case self::$ACTION_ENABLE_INTEGRATION:
        IntegrationRepository::updateIntegration($data["id"], $data["enable"]);
        break;

      case self::$ACTION_GET_ALL_INTEGRATIONS:
        $response = IntegrationRepository::getAllIntegrations();
        $this->sendResponse(true, $response);
        break;

      case self::$ACTION_GET_USER_TYPE:
        $this->sendSessionKey("tipo");
        break;*/

      default:
        break;

    }
  }
}

$calendarCampaingController = new CalendarCampaingController();
$calendarCampaingController->handlePostRequest();