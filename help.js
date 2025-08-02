const helpForm = document.getElementById("help-form");
const submitButton = document.getElementById("submit");
const submitMessage = document.getElementById("submit-message");

helpForm.addEventListener("submit", async function (event) {
  event.preventDefault();
  submitButton.disabled = true;
  submitButton.textContent = "Sending...";
  const formData = new FormData(event.target);
  await sendHelp(formDataToJson(formData)).then((response) => {
    if (response.ok) {
      console.log("Success!");
      const successElement = document.createElement("h2");
      successElement.textContent = "Message Sent!";
      helpForm.insertAdjacentElement("beforebegin", successElement);
      helpForm.remove();
    } else {
      console.warn("Failure");
      submitMessage.classList.add("failure");
      submitMessage.textContent =
        "Message could not be submitted. Please try again.";
      submitButton.disabled = false;
      submitButton.textContent = "Send Message";
    }
  });
});

// Accepts JSON-formatted form data
async function sendHelp(formData) {
  const response = await fetch("/send-help", {
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
