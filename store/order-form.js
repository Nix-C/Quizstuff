// Select form and prevent submission default
const orderForm = document.getElementById("order-form");
const submitMessage = document.getElementById("submit-message");
const submitButton = document.getElementById("button--submit");

// // Track form changes to update total est price
// orderForm.addEventListener("change", async function (event) {
//   const [type, productId, variantId] = event.target.id.split("_");
//   if (type === "product") {
//     console.log("Update total");
//   }
// });

orderForm.addEventListener("submit", async function (event) {
  event.preventDefault();
  const formData = new FormData(event.target);
  const orderData = formatOrderData(formData);
  console.log(orderData);

  if (orderData.lineItems.length > 0) {
    submitButton.disabled = true;
    submitButton.innerText = "Submitting...";
    await sendOrderData(orderData)
      .then((response) => {
        if (response.ok) {
          // submitMessage.innerHTML = "Order submitted successfully.";
          // submitMessage.classList = "success";
          replaceContent(
            orderForm,
            `
            <h2>Thank You</h2>
            <p>Your order has been successfully submitted.</p>
            <p style="color:#bbd7e5"><strong>** Shipping costs will be added to the total when your order is confirmed. Your updated total will be sent after the initial order confirmation email.</strong></p>
            <button onclick="location.reload();">Create New Order</button>
            `
          );
          window.scrollTo(0, 0);
        } else {
          submitButton.disabled = false;
          submitButton.innerText = "Submit Order";
          submitMessage.innerHTML = "Order failed to submit.";
          submitMessage.classList = "failure";
        }
      })
      .catch((error) => {
        submitButton.disabled = false;
        submitButton.innerText = "Submit Order";
        submitMessage.innerHTML = "Order failed to submit.";
        submitMessage.classList = "failure";
        console.error(error);
      });
  } else {
    submitMessage.innerHTML = "Please add products.";
    submitMessage.classList = "failure";
  }
});

function replaceContent(el, newContent) {
  el.insertAdjacentHTML("afterEnd", newContent);
  el.remove();
}

function incrementQty(inputId) {
  const input = document.getElementById(inputId);
  const value = parseInt(input.value);
  const max = parseInt(input.max);
  if (value + 1 <= max) {
    input.value = value + 1;
    input.dispatchEvent(new Event("change", { bubbles: true })); // Manually trigger change
  }
}
function decrementQty(inputId) {
  const input = document.getElementById(inputId);
  const value = parseInt(input.value);
  const min = parseInt(input.min);
  if (value - 1 >= min) {
    input.value = value - 1;
    input.dispatchEvent(new Event("change", { bubbles: true })); // Manually trigger change
  }
}

function formatOrderData(formData) {
  // Declare input types
  const validAddressTypes = [
    "name-first",
    "name-last",
    "address-1",
    "address-2",
    "city",
    "state",
    "zip",
    "email",
    "phone",
  ];
  const validLineItemTypes = ["product", "variant", "option"];

  // Empty orderData
  let orderData = { shippingInfo: {}, lineItems: [] };

  // Process formData
  for (let [key, value] of formData.entries()) {
    /**
     * Input name format: type_productId_typeId
     * type: 'product', 'variant', 'option'
     * productId: product id
     * typeId: variant id or option id
     */
    const [type, productId, typeId] = key.split("_");

    if (validAddressTypes.includes(type)) {
      // Capture address inputs
      orderData.shippingInfo[camelize(type)] = value;
    } else if (
      validLineItemTypes.includes(type) &&
      (value > 0 || value == "true")
    ) {
      // Capture line items
      const newLineItem = {
        productId: productId,
        variantId: type === "variant" ? typeId : null,
        optionId: type === "option" ? typeId : null,
        quantity: type === "option" ? "0" : value,
      };
      orderData.lineItems.push(newLineItem);
    }
  }

  // Update option quantities so the number of product quantities
  for (optionItem of orderData.lineItems.filter(
    (lineItem) => lineItem.optionId !== null
  )) {
    let quantity = 0;
    for (productItem of orderData.lineItems.filter(
      (lineItem) =>
        // A lineitem with a matching productId and a NULL option id
        // (Not an option item)
        lineItem.productId == optionItem.productId && lineItem.optionId == null
    )) {
      // Add product quantity to total
      quantity += parseInt(productItem.quantity);
    }
    // Assign quantity to option
    optionItem.quantity = quantity.toString();
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
  const response = await fetch("/store/process-order", {
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
      {productId: '1', variantId: null, optionId: null, quantity: '1'},
      {productId: '2', variantId: '1', optionId: null, quantity: '2'},
      {productId: '2', variantId: '3', optionId: null, quantity: '1'},
      {productId: '3', variantId: null, optionId: null, quantity: '1'},
      {productId: '3', variantId: null, optionId: '1', quantity: '1'},
    }
  ]
}
 */
