@extends('layouts.app')

@section('content')
<!-- Sample CSS styles for demo purposes. You can override these styles to match your web page's branding. -->
<link rel="stylesheet" type="text/css" href="https://www.paypalobjects.com/webstatic/en_US/developer/docs/css/cardfields.css"/>

<!-- JavaScript SDK -->
<script src="https://www.paypal.com/sdk/js?components=buttons,hosted-fields&client-id={{ $data['client_id'] }}" data-client-token="{{ $data['client_token'] }}"></script>

<div>
    <h3 align="center">Payment Amount: {{ $data['amount'] }} {{ $data['currency'] }}</h3>
</div>
<!-- Buttons container -->
<table border="0" align="center" valign="top" bgcolor="#FFFFFF" style="width: 39%">
    <tr>
    <td colspan="2">
        <div id="paypal-button-container"></div>
    </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
</table>

<div align="center"> or </div>

<!-- Advanced credit and debit card payments form -->
<div class="card_container">
    <form id="card-form">
        <label for="card-number">Card Number</label>
        <div id="card-number" class="card_field"></div>
        <div>
            <label for="expiration-date">Expiration Date</label>
            <div id="expiration-date" class="card_field"></div>
        </div>
        <div>
            <label for="cvv">CVV</label><div id="cvv" class="card_field"></div>
        </div>
        <label for="card-holder-name">Name on Card</label>
        <input type="text" id="card-holder-name" name="card-holder-name" autocomplete="off" placeholder="card holder name"/>
        <div>
            <label for="card-billing-address-street">Billing Address</label>
            <input type="text" id="card-billing-address-street" name="card-billing-address-street" autocomplete="off" placeholder="street address"/>
        </div>
        <div>
            <label for="card-billing-address-unit">&nbsp;</label>
            <input type="text" id="card-billing-address-unit" name="card-billing-address-unit" autocomplete="off" placeholder="unit"/>
        </div>
        <div>
            <input type="text" id="card-billing-address-city" name="card-billing-address-city" autocomplete="off" placeholder="city"/>
        </div>
        <div>
            <input type="text" id="card-billing-address-state" name="card-billing-address-state" autocomplete="off" placeholder="state"/>
        </div>
        <div>
            <input type="text" id="card-billing-address-zip" name="card-billing-address-zip" autocomplete="off" placeholder="zip / postal code"/>
        </div>
        <div>
            <input type="text" id="card-billing-address-country" name="card-billing-address-country" autocomplete="off" placeholder="country code" />
        </div>
        <br><br>
        <button value="submit" id="submit" class="btn btn-success">Pay</button>
    </form>
</div>

<!-- Implementation -->
<script>
    let orderId;

    // Displays PayPal buttons
    paypal.Buttons({
        style: {
            layout: 'horizontal'
        },
        createOrder: function(data, actions) {
            return actions.order.create({
                intent: "CAPTURE",
                purchase_units: [{
                    amount: {
                        value: "{{ $data['amount'] }}",
                        currency: "{{ $data['currency'] }}"
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                console.log('paypal account payment detail');
                console.log(details);
                window.location.href = '/payment-success';
            });
        }
    }).render("#paypal-button-container");

    // If this returns false or the card fields aren't visible, see Step #1.
    if (paypal.HostedFields.isEligible()) {

        // Renders card fields
        paypal.HostedFields.render({
            // Call your server to set up the transaction
            createOrder: function () {
                return fetch("{{ route('create.order') }}", {
                    method: 'post',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        amount: "{{ $data['amount'] }}",
                        currency: "{{ $data['currency'] }}"
                    })
                }).then(function(res) {
                    console.log(res);
                    return res.json();
                }).then(function(orderData) {
                    orderId = orderData.id;
                    return orderId;
                });
            },

            styles: {
                '.valid': {
                    'color': 'green'
                },
                '.invalid': {
                    'color': 'red'
                }
            },

            fields: {
                number: {
                    selector: "#card-number",
                    placeholder: "4111 1111 1111 1111"
                },
                cvv: {
                    selector: "#cvv",
                    placeholder: "123"
                },
                expirationDate: {
                    selector: "#expiration-date",
                    placeholder: "MM/YY"
                }
            }
        }).then(function (cardFields) {
            document.querySelector("#card-form").addEventListener('submit', (event) => {
                event.preventDefault();

                cardFields.submit({
                    // Cardholder's first and last name
                    cardholderName: document.getElementById('card-holder-name').value,
                    // Billing Address
                    billingAddress: {
                        // Street address, line 1
                        streetAddress: document.getElementById('card-billing-address-street').value,
                        // Street address, line 2 (Ex: Unit, Apartment, etc.)
                        extendedAddress: document.getElementById('card-billing-address-unit').value,
                        // State
                        region: document.getElementById('card-billing-address-state').value,
                        // City
                        locality: document.getElementById('card-billing-address-city').value,
                        // Postal Code
                        postalCode: document.getElementById('card-billing-address-zip').value,
                        // Country Code
                        countryCodeAlpha2: document.getElementById('card-billing-address-country').value
                    }
                }).then(function () {
                    // Payment was successful! Show a notification or redirect to another page.
                    window.location.replace('/payment-success');
                }).catch(function (err) {
                    alert('Payment could not be captured! ' + JSON.stringify(err))
                });
            });
        });
    } else {
        // Hides card fields if the merchant isn't eligible
        document.querySelector("#card-form").style = 'display: none';
    }
</script>
@endsection