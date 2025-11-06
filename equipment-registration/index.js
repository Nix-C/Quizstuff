const form = document.getElementById("form");

form.addEventListener("submit", async function (event) {
  console.log("Submit!");
  event.preventDefault();
  const formData = new FormData(event.target);
  const formattedData = formatFormData(formData);

  // Send data
  console.log(formattedData);
});

function formatFormData(formData) {
  const entries = Array.from(formData.entries());
  return {
    event: formData.get("event") ?? null,
    nameFirst: formData.get("name_first") ?? null,
    nameLast: formData.get("name_last") ?? null,
    district: formData.get("district") ?? null,
    email: formData.get("email") ?? null,
    phone: formData.get("phone") ?? null,
    laptops: group("laptop"),
    pads: group("pad"),
    interfaces: group("interface"),
    monitors: group("monitor"),
    projectors: group("projector"),
    powerstrips: group("powerstrip"),
    extensions: group("extension"),
    others: group("other"),
  };

  function group(type) {
    const grouped = {};

    // Match pattern like "laptop-operating_system-1"
    const regex = new RegExp(`^${type}-(.+)-(\\d+)$`);

    for (const [key, value] of entries) {
      const match = key.match(regex);
      if (!match) continue;

      const [_, field, index] = match;

      if (!grouped[index]) grouped[index] = {};
      grouped[index][field] = value ?? null;
    }
    return Object.values(grouped); // returns array instead of object
  }
}
