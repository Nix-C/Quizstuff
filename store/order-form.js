// console.log("Order form ready");

// Select form and prevent submission default
const orderForm = document.querySelector("#order-form");
const submitMessage = document.querySelector("#submit-message");
orderForm.addEventListener("submit", async function (event) {
  event.preventDefault();
  const formData = new FormData(event.target);
  const orderData = formatOrderData(formData);
  // console.log(orderData);

  await sendOrderData(orderData)
    .then((response) => {
      if (response.ok) {
        submitMessage.innerHTML = "Order submitted successfully.";
      } else {
        submitMessage.innerHTML = "Order failed to submit.";
      }
    })
    .catch((error) => {
      submitMessage.innerHTML = "Order failed to submit.";
      console.error(error);
    });
});

function formatOrderData(formData) {
  const validAddressTypes = [
    "name-first",
    "name-last",
    "address",
    "city",
    "state",
    "zip",
    "email",
    "phone",
  ];
  const validLineItemTypes = ["product", "variant", "option"];
  let orderData = { shippingInfo: {}, lineItems: [] };

  for (let [key, value] of formData.entries()) {
    /**
     * Input name format: type_productId_typeId
     * type: 'product', 'variant', 'option'
     * productId: product id
     * typeId: variant id or option id
     */
    const [type, productId, typeId] = key.split("_");

    // Evaluate form inputs (ignore bad inputs)
    if (validAddressTypes.includes(type)) {
      orderData.shippingInfo[camelize(type)] = value;
    } else if (validLineItemTypes.includes(type)) {
      if (value > 0 || value == "true") {
        // Find index of existing product in lineItems
        let lineItemIndex = orderData.lineItems.findIndex(
          (lineItem) => lineItem.id === productId
        );

        // If option is checked and product exists, add option to product
        if (type === "option" && lineItemIndex !== -1) {
          // If options array does not exist, create it
          if (!orderData.lineItems[lineItemIndex]["options"]) {
            orderData.lineItems[lineItemIndex]["options"] = [];
          }
          orderData.lineItems[lineItemIndex]["options"].push({
            id: typeId,
            checked: value === "true",
          });
        } else if (type === "product" || type === "variant") {
          // If product does not exist, add new line item
          if (lineItemIndex === -1) {
            lineItemIndex = orderData.lineItems.push({
              id: productId,
            });
            lineItemIndex = lineItemIndex - 1;
          }

          // Add variant to line item
          if (type === "variant") {
            // If variants array does not exist, create it
            if (!orderData.lineItems[lineItemIndex]["variants"]) {
              orderData.lineItems[lineItemIndex]["variants"] = [];
            }
            orderData.lineItems[lineItemIndex]["variants"].push({
              id: typeId,
              quantity: value,
            });
          }
        }
      }
    }
  }

  return orderData;
}

// Camelize string 🤖
function camelize(str) {
  return str
    .replace(/(?:^\w|[A-Z]|\b\w)/g, function (word, index) {
      return index === 0 ? word.toLowerCase() : word.toUpperCase();
    })
    .replace(/[\s-]+/g, ""); // Removes spaces and hyphens
}

async function sendOrderData(orderData) {
  const response = await fetch("/store/js-test.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(orderData),
  });
  return response;
}
/**
 * Example orderData
 {
  "shippingInfo": {
    "nameFirst": "John",
    "nameLast": "Doe",
    "address": "1234 Elm St.",
    "city": "Springfield",
    "state": "IL",
    "zip": "62701",
    "phone": "555-555-1234",
    "email": "johndoe@example.com"
  },
  "line-items": [
    {
      "id": 1,
      "quantity": 1,
      "variants": [
        {
          "id": 1,
          "quantity": 1
        }
      ],
      "options": [
        {
          "id": 1,
          "checked": false
        }
      ]
    }
  ]
}
 */
