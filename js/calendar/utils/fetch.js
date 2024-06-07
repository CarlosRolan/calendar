import { ACTION_INIT_CALENDAR } from "./actions.js";

async function getCampaingsData() {
  const data = {
    action: ACTION_INIT_CALENDAR,
  };

  try {
    const response = await fetch(
      "./controllers/CalendarCampaingController.php",
      {
        method: "POST",
        body: JSON.stringify(data),
      }
    );

    const json = await response.json();

    console.log(json);

    return json.data.campaings;
  } catch (error) {
    console.log(error);
  }
}

export { getCampaingsData };
