<?php

//Carlos Rolan
namespace Repositories;

require_once __DIR__ . "/BaseRepository.php";

use Repositories\BaseRepository;

class CalendarCampaingRepository extends BaseRepository
{

  public static function getAllCampaingEvents()
  {

    //IMPORTANT Sin fetch mode porque en el controllador se construlle el objecto Calendar
    $stmt = self::getDBInstance()->prepare("SELECT * FROM campaign ORDER BY Activa DESC, Fecha_inicio ASC");

    $stmt->execute();

    //$stmt->setFetchMode(PDO::FETCH_CLASS, CalendarEvent::class);

    $result = $stmt->fetchAll();

    return $result;
  }

}