var stripe = stripeKey;
var elements = stripe.elements();
var cardNumber = elements.create('cardNumber');
cardNumber.mount('#card-number-element');
var cardExpiry = elements.create('cardExpiry');
cardExpiry.mount('#card-expiry-element');
var cardCvc = elements.create('cardCvc');
cardCvc.mount('#card-cvc-element');

cardNumber.on('change', function(event) {
  setOutcome(event);
});

cardExpiry.on('change', function(event) {
  setOutcome(event);
});

cardCvc.on('change', function(event) {
  setOutcome(event);
});

function setOutcome(result) {
    var errorElement = document.getElementById('card-errors');
    errorElement.textContent = '';
    if(result.error){
    	errorElement.textContent = result.error.message;
   }
}

function stripeTokenHandler(token) {
  // Insert the token ID into the form so it gets submitted to the server
  var form = document.getElementById('stripe-form');
  var hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form.appendChild(hiddenInput);

  // Submit the form
  form.submit();
}

function createToken() {
  stripe.createToken(cardNumber).then(function(result) {
    if (result.error) {
  			setOutcome(result);
    } else {
      // Send the token to your server
      stripeTokenHandler(result.token);
    }
  });
};

// Create a token when the form is submitted.
var form = document.getElementById('stripe-form');
form.addEventListener('submit', function(e) {
  e.preventDefault();
  createToken();
});