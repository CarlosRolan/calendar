<?php

namespace Models;

require_once __DIR__ . '/../repositories/PlaylistRepository.php';

//Carlos Rolan
use DateTime;
use JsonSerializable;
use Repositories\PlaylistRepository;

class Campaing implements JsonSerializable
{

 public $Id;
 public $Nombre;
 public $Seccion;

 public $Fecha_inicio;

 public $Fecha_fin;

 public $Activa;

 public $Status;

 public $Mute;

 public $Preferente;

 public $Codigo_mando;

 public $Codigo_mandoBorrado;

 public $IdCentral;

 public function isInDate()
 {
  if ($this->Status == "G") {
   return true;
  } else {
   $startDate = new DateTime($this->Fecha_inicio);
   $endDate = new DateTime($this->Fecha_fin);

   $todayDate = new DateTime();

   return ($todayDate >= $startDate && $todayDate <= $endDate);
  }
 }

 /**
  * Para saber si la campaña tiene alguna imagen o algun video
  */
 public function hasPlaylist()
 {
  $repoInstance = new PlaylistRepository();
  $playlist = $repoInstance->getByCampaign($this->Id);

  if (count($playlist) == 0) {
   return false;
  } else {
   var_dump($playlist);
   return true;
  }

 }

 // Implementación del método jsonSerialize
 public function jsonSerialize()
 {
  return [
   'Id' => $this->Id,
   'Nombre' => $this->Nombre,
   'Seccion' => $this->Seccion,
   'Fecha_inicio' => $this->Fecha_inicio,
   'Fecha_fin' => $this->Fecha_fin,
   'Activa' => $this->Activa,
   'Status' => $this->Status,
   'Mute' => $this->Mute,
   'Preferente' => $this->Preferente,
   'Codigo_mando' => $this->Codigo_mando,
   'Codigo_mandoBorrado' => $this->Codigo_mandoBorrado,
   'IdCentral' => $this->IdCentral,
  ];
 }

}