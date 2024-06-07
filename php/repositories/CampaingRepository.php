<?php
//Carlos Rolan
namespace Repositories;

require_once __DIR__ . '/../models/Campaing.php';
require_once __DIR__ . "/BaseRepository.php";

use Repositories\BaseRepository;
use Models\Campaing;

use PDO;
use PDOException;

class CampaingRepository extends BaseRepository
{

 public static function getAllCampaings()
 {
  // hay que añadir el día, para que solo saque los del día actual
  $stmt = self::getDBInstance()->prepare("SELECT * from campaign");
  $stmt->execute();

  $stmt->setFetchMode(PDO::FETCH_CLASS, Campaing::class);
  $result = $stmt->fetchAll();

  return $result;
 }

}