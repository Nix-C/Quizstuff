/**
 * TODO:
 * - Add password verification (re-type password)
 * - Add Cloudflare turnstile
 * - Style entries
 * - Confirm/Add mic entry
 */

const form = document.getElementById("form");
const addButtons = document.querySelectorAll('[id^="add-"]');

// Enable add-buttons
addButtons.forEach((button) => {
  button.onclick = (event) => {
    event.preventDefault();

    const container = button.parentElement;
    const currentCount = container.querySelectorAll("fieldset").length;
    const type = button.getAttribute("data-type");

    container.insertBefore(createNewEntry(type, currentCount), button);
  };
});

// Pre-add equipment
const laptopsAddButton = document.getElementById("add-laptop");
laptopsAddButton.parentElement.insertBefore(
  createNewEntry("laptop"),
  laptopsAddButton
);

// Submit action
form.addEventListener("submit", async function (event) {
  console.log("Submit!");
  event.preventDefault();
  const formData = new FormData(event.target);
  let formattedData;

  try {
    formattedData = formatFormData(formData);
  } catch (error) {
    console.error("Error parsing form;", error);
  }

  // Send data
  if (formattedData && formattedData.equipment.length != {}) {
    sendData(formattedData)
      .then((response) => {
        if (response.ok) {
        } else {
          console.error("Server returned with an error;", response.error);
        }
      })
      .catch((error) => console.error("Error sending data;", error));
  } else {
    console.error("No Equipment!", formattedData);
  }
});

// Create New Entry
function createNewEntry(type, currentCount = 0) {
  const deleteButton = document.createElement("button");
  deleteButton.classList = "button--delete";
  deleteButton.innerHTML = "X";
  deleteButton.onclick = (event) => {
    event.preventDefault();
    event.target.parentElement.remove();
  };

  const newEntry = document.createElement("fieldset");
  newEntry.id = `${type}-${currentCount}`;
  newEntry.innerHTML = document
    .getElementById(`${type}-template`)
    .innerHTML.replaceAll("?", `${currentCount}`);

  newEntry.appendChild(deleteButton);

  return newEntry;
}

// Format form data to send
function formatFormData(formData) {
  const entries = Array.from(formData.entries());
  return {
    event: formData.get("event") ?? null,
    nameFirst: formData.get("name_first") ?? null,
    nameLast: formData.get("name_last") ?? null,
    district: formData.get("district") ?? null,
    email: formData.get("email") ?? null,
    phone: formData.get("phone") ?? null,
    equipment: {
      laptops: group("laptop"),
      pads: group("pads"),
      interfaces: group("interface"),
      monitors: group("monitor"),
      projectors: group("projector"),
      powerstrips: group("powerstrip"),
      extensions: group("extension"),
      others: group("other"),
    },
    token: formData.get("cf-turnstile-response"),
  };

  function group(type) {
    const grouped = {};

    // Match pattern like "laptop-operating_system-1"
    const regex = new RegExp(`^${type}-(.+)-(\\d+)$`); // ${type}-(string)-(number)

    for (const [key, value] of entries) {
      const match = key.match(regex);
      if (!match) continue;

      const [_, field, index] = match; // Grabbing the first 3 elements of the match array

      if (!grouped[index]) grouped[index] = {};
      grouped[index][field] = value;
    }
    return Object.values(grouped); // returns array instead of object
  }
}

async function sendData(data) {
  const response = await fetch("/equipment-registration/submit.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });
  return response;
}
