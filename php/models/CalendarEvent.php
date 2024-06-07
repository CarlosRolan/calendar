<?php
namespace Models;

use DateTime;
use JsonSerializable;

require_once __DIR__ . "/../repositories/SeccionRepository.php";

use Repositories\SeccionRepository;

class CalendarEvent implements JsonSerializable
{
 private static $INDEX_COLORS = 0;
 private static $DEFAULT_COLORS = array("#39B178", "#23ADBE", "#E64147", "#1E6BB8", "#B1BE28", "#9A5295");
 private static $NO_DATE = "0000-00-00";
 private static $START_DATE = "2020-01-01";
 private static $END_DATE = "2030-01-01";
 private static $WHITE_COLOR = "#ffffff";

 private $id;
 private $name;
 private $date_start;
 private $date_end;
 private $color;
 private $section;
 private $preferent;  //Boolean: es preferente?
 private $permanent;  //Boolean: es general?
 private $active;  //Boolean: es activa?

 public function getName()
 {
  return $this->name;
 }

 public function isPermanent()
 {
  return ($this->permanent == "G");
 }

 public function isInDate()
 {
  if ($this->isPermanent()) {
   return true;
  } else {
   $startDate = new DateTime($this->date_start);
   $endDate = new DateTime($this->date_end);

   $todayDate = new DateTime();

   return ($todayDate >= $startDate && $todayDate <= $endDate);
  }
 }

 /**
  * IMPORTANTE
  * El valor de la base de datos campaing tiene la siguiente estructura
  * Id, Nombre, Seccion, Activa, Codigo_mando, Codigo_mandoBorrado ,Fecha_fin, Fecha_inicio, IdCentral, Mute, Preferente, Status
  */
 public function __construct($campaing)
 {
  $this->id = $campaing->Id;
  $this->name = $campaing->Nombre;
  $this->section = $campaing->Seccion;

  if ($campaing->Preferente == "T") {
   $this->preferent = true;
  } else {
   $this->preferent = false;
  }

  if ($campaing->Status == "G") {
   $this->permanent = true;
  } else {
   $this->permanent = false;
  }

  //Si la fecha es 0000-00-00 pongouna fecha predeterminada que emula el efecto de campaña permanente
  if ($campaing->Fecha_inicio == CalendarEvent::$NO_DATE) {
   $this->date_start = self::$START_DATE;
  } else {
   $this->date_start = $campaing->Fecha_inicio;
  }

  if ($campaing->Fecha_fin == CalendarEvent::$NO_DATE) {
   $this->date_end = self::$END_DATE;
  } else {
   $this->date_end = $campaing->Fecha_fin;
  }

  //El color del evento es el mismo que el FONDO de la seccion y si no, es el predeterminado
  $background_color = SeccionRepository::getByCodeWithName($this->section)->getColorFondo();

  if ($background_color != self::$WHITE_COLOR) {
   $this->color = $background_color;
  } else {
   $this->color = self::$DEFAULT_COLORS[self::$INDEX_COLORS++];

   if (self::$INDEX_COLORS > 5) {
    self::$INDEX_COLORS = 0;
   }
  }

  if ($campaing->Activa == "T") {
   $this->active = true;
  } else {
   $this->active = false;
   //Si no esta activa reduzco la opacidad de el color
   $this->color = $this->reduceOpacity($this->color);
  }

 }

 public function reduceOpacity($color_hex)
 {
  // Convertir el código de color a RGB
  $r = hexdec(substr($color_hex, 1, 2));
  $g = hexdec(substr($color_hex, 3, 2));
  $b = hexdec(substr($color_hex, 5, 2));

  // Ajustar el factor de opacidad si está fuera del rango [0, 1]
  $opacity = max(0, min(0.4, 1));

  // Calcular el valor alfa
  $a = round(255 * $opacity);

  // Formatear el nuevo código de color en hexadecimal con opacidad
  return sprintf("#%02X%02X%02X%02X", $r, $g, $b, $a);
 }

 // Método de la interfaz JsonSerializable
 public function jsonSerialize()
 {
  return [
   //NO TOCAR las keys de (id, start, end, color) son necesarias para los eventos del Calendar de FullCalendar
   'id' => $this->id,
   'title' => $this->name,
   'start' => $this->date_start,
   'end' => $this->date_end,
   'section' => $this->section,
   'color' => $this->color,
   'preferent' => $this->preferent,
   'permanent' => $this->permanent,
   'active' => $this->active,
  ];
 }
}