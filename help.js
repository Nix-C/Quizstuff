const helpForm = document.getElementById("help-form");

helpForm.addEventListener("submit", async function (event) {
  event.preventDefault();
  const formData = new FormData(event.target);
  await sendHelp(formDataToJson(formData)).then((response) => {
    if (response.ok) console.log("Success!");
    else console.warn("Failure");
  });
});

// Accepts JSON-formatted form data
async function sendHelp(formData) {
  const response = await fetch("/send-help.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  });
  return response;
}

// 🤖
function formDataToJson(formData) {
  const jsonObject = {};
  for (const [key, value] of formData.entries()) {
    // If the key already exists, convert to an array or push to existing array
    if (jsonObject.hasOwnProperty(key)) {
      if (Array.isArray(jsonObject[key])) {
        jsonObject[key].push(value);
      } else {
        jsonObject[key] = [jsonObject[key], value];
      }
    } else {
      jsonObject[key] = value;
    }
  }
  return jsonObject;
}
