const form = document.getElementById("form");

form.addEventListener("submit", async function (event) {
  console.log("Submit!");
  event.preventDefault();
  const formData = new FormData(event.target);
  const formattedData = formatFormData(formData);

  console.log(formattedData);
});

function formatFormData(formData) {
  const laptopData = Array.from(formData.entries()).filter(([key, _]) =>
    key.startsWith("laptop")
  );
  const padData = Array.from(formData.entries()).filter(([key, _]) =>
    key.startsWith("pad")
  );
  const interfaceData = Array.from(formData.entries()).filter(([key, _]) =>
    key.startsWith("interface")
  );

  console.log(laptopData);
  return {
    nameFirst: formData.get("name_first") ?? null,
    nameLast: formData.get("name_last") ?? null,
    district: formData.get("district") ?? null,
    email: formData.get("email") ?? null,
    phone: formData.get("phone") ?? null,
  };
}
