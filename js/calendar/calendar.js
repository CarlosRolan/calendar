import { getCampaingsData } from "./utils/fetch.js";

//Se recogen los datos de todas las campañas
const campaings = await getCampaingsData();
//Se inicializa el objecto calendar y se inserta en el contenedor de publi
const calendar = createCalendar(campaings);

//IMOPRTANT
function addExtraDayEvents(campaings) {
  campaings.forEach((camp) => {
    //OJO! se añade un dia a las campañas que no son permanentes es decir a las que tenga un rango de fechas
    if (!camp.permanent) {
      const date = new Date(camp.end);
      date.setDate(date.getDate() + 1);
      const formated = date.toISOString().split("T")[0];
      camp.end = formated;
    }
  });

  return campaings;
}

//TODO cuando haces click en un evento se queda pillada la animacion de toggle del evento
function createCalendar(dataEvents) {
  //Asignamos un dia mas a la fecha final de las campañas permanentes
  const dataEventsPLus1Day = addExtraDayEvents(dataEvents);

  var calendarContainer = document.getElementById("calendarContainer");

  var calendar = new FullCalendar.Calendar(calendarContainer, {
    initialView: "dayGridMonth", // Vista de agenda semanal
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,dayGridWeek",
    },
    // Configuración de eventos de muestra
    showNonCurrentDates: false,
    fixedWeekCount: false,
    events: dataEventsPLus1Day,
    navLinks: true,
    allDaySlot: true, // Mostrar la fila de eventos de todo el día

    buttonText: {
      today: "Hoy", // Cambiar el texto del botón "Today" a "Hoy"
      month: "Mes", // Cambiar el texto del botón "Month" a "Mes"
      week: "Semana", // Cambiar el texto del botón "Week" a "Semana"
      day: "Día", // Cambiar el texto del botón "Day" a "Día"
    },

    dateClick: function (info) {
      this.changeView("dayGridDay", info.date);
    },

    eventClick: function (info) {
      //Funcion implementada para mejor mantenimiento del codigo
      onEventClick(info);
    },

    eventContent: function (arg) {
      //IMPORTANTE el return .Funcion implementada para mejor mantenimiento del codigo
      return getEventContent(arg);
    },

    //Configuracion para que sea un calendario ESPAÑOL
    locale: "es", // Establece el idioma a español
    firstDay: 1, //establece que el primer dia de la semana sea el lunes
  });

  return calendar;
}

function getEventContent(arg) {
  const { id, title } = arg.event;
  const { section, preferent, permanent, active } = arg.event.extendedProps;

  //let preferentMsg = "";
  //let activeMsg = "";
  let permanentMsg = "";

  if (permanent == true) {
    permanentMsg = '<div class="eventElement">Permanente</div>';
  }

  // if (preferent == true) {
  // 	preferentMsg = '<div>Preferente</div>';
  // }

  // if (active == true) {
  // 	activeMsg = '<div>Activa</div>';
  // }

  //tiene que ser extended Props ya que section no existe originalmente en el objeto event
  return {
    html:
      '<div class="eventContent">' +
      '<div class="eventElement">' +
      title +
      "</div>" +
      '<div class="eventElement">' +
      section +
      "</div>" +
      //preferentMsg +
      permanentMsg +
      //activeMsg +
      "</div>",
  };
}

function onEventClick(info) {
  const { title, id, startStr, endStr } = info.event;
  const { section, preferent, permanent, active } = info.event.extendedProps;

  let preferentMsg = "";
  let permanentMsg = "";
  let activeMsg = "";

  let dateRange =
    "<h4><center>" + startStr + " hasta " + endStr + "</center></h4>";

  if (preferent) {
    preferentMsg = '<h4 style="color: orange"><center>Preferente</center></h4>';
  } else {
    preferentMsg = "<h4><center>No Preferente</center></h4>";
  }

  if (permanent) {
    permanentMsg = '<h4 style="color: orange"><center>Permanente</center></h4>';
  } else {
    permanentMsg = "<h4><center>No permanente</center></h4>";
  }

  if (active) {
    activeMsg = '<h4 style="color: green" ><center>Activa</center></h4>';
  } else {
    activeMsg = '<h4 style="color: red"><center>No Activa</center></h4>';
  }

  if (preferent || permanentMsg) {
    dateRange = "";
  }

  const content =
    "<h3><center>" +
    title +
    "</center></h3>" +
    dateRange +
    activeMsg +
    permanentMsg +
    preferentMsg;

  showSweetAlert("Ir a edición de campaña: ", content, () => {
    location.href = "campaign.php?id=" + id;
  });

  return false;
}

function showCalendarCampaing(loadingTimeMs) {
  const calendarContainer = document.getElementById("calendarContainer");
  setTimeout(() => {
    calendarContainer.style.display = "flex";
    //se oculta el div de icono de carga
    hideCalendarLoader();
    calendar.render();
  }, loadingTimeMs);
}

function hideCalendarCampaing() {
  showCalendarLoader();
  const calendarContainer = document.getElementById("calendarContainer");
  calendarContainer.style.display = "none";
}

function showSweetAlert(title, content, callback) {
  // Implementa esta función según SweetAlert
  // Puedes usar Swal.fire() o cualquier otra función que se adapte a tu caso
  // Por ejemplo:
  Swal.fire({
    title: title,
    html: content,
    imageUrl: "./imagenes/general/logo_gmedia_asys_purple.png",
    showDenyButton: false,
    showCancelButton: true,
  }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
      callback();
    }

    if (result.isCanceled) {
      this.close();
    }
  });
}

function initEvents() {
  //Se carga el calendario a los 5 segundos
  $(document).ready(function () {
    showCalendarCampaing(5000);
  });

  //Cada vez que se cambia la pestaña de publi se recarga el calendario
  window.addEventListener("hashchange", (event) => {
    hideCalendarCampaing();

    if (event.newURL.endsWith("#calendarCamp")) {
      showCalendarCampaing(5000);
    }
  });
}

function showCalendarLoader() {
  const calendarLoader = document.getElementById("calendarLoader");
  calendarLoader.style.display = "flex";
}

function hideCalendarLoader() {
  const calendarLoader = document.getElementById("calendarLoader");
  calendarLoader.style.display = "none";
}

function showCalendarTab() {
  const calendarTab = document.getElementById("calendarTabId");
  calendarTab.style.display = "default";
}

function hideCalendarTab() {
  const calendarTab = document.getElementById("calendarTabId");
  calendarTab.style.display = "none";
}

async function startCalendar() {
  if (campaings.length != 0) {
    showCalendarTab();
    initEvents();
  } else {
    hideCalendarTab();
  }
}

export { startCalendar };
