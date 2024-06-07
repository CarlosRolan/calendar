<?php
namespace Models;

require_once __DIR__ ."/../models/CalendarEvent.php";

use JsonSerializable;
use Models\CalendarEvent;

//Carlos Rolan
class CalendarCampaing implements JsonSerializable
{
    private $calendarEvents = [];

     //type : CalendarEvent[]
    public function __construct($campaings)
    {
        if (isset($campaings) && is_array($campaings)) {
            foreach ($campaings as $event) {
                $this->calendarEvents[] = $event;
            }
        }
    }

    // Getters
    public function getEvents()
    {
        return $this->calendarEvents;
    }


    // Método de la interfaz JsonSerializable
    public function jsonSerialize()
    {
        return [
            'campaings' => $this->calendarEvents,
        ];
    }
}