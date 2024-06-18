
    @push('checkoutbraintree')
    <script src="https://js.braintreegateway.com/web/dropin/1.30.0/js/dropin.min.js"></script>
    <script>
        var tokens='';
$(document).ready(function() {

  
            $.ajax({
                url: '{{route('payments.braintree.gettoken')}}',
                type: 'GET',
                success: function(token) {
                    // Display the fetched token
                    tokens=token;
                setTimeout(function() {
             $('#dropin-container').html(``);
                loadpayment(tokens);

            }, 1000);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching token:', error);
                }
            });

      

        });

function checkerd(s){
    if ($(s).is(':checked')) {
       
        setTimeout(function() {
             $('#dropin-container').html(``);
                loadpayment(tokens);

            }, 3000);
           
        }
}



        function loadpayment(token) {
    var form = document.getElementById('checkout-form');
    var submitButton = document.getElementById('submit-buttons'); // Changed ID to 'submit-buttons'
    var instance;
    braintree.dropin.create({
        authorization: token,
        container: '#dropin-container'
    }, function (createErr, newInstance) {
        if (createErr) {
            console.error(createErr);
            return;
        }
        instance = newInstance;
    });

    submitButton.addEventListener('click', function (event) {
        event.preventDefault();
        submitButton.disabled = true;
        instance.requestPaymentMethod(function (err, payload) {
            if (err) {
                console.error(err);
                return;
            }
            document.getElementById('payment_method_nonce').value = payload.nonce;
            // Add any additional logic here if needed

            form.submit();
        });
    });
}

    

        var form = document.getElementById('checkout-form');
        form.onchange = function(event) {
          
      setTimeout(function() {
             $('#dropin-container').html(``);
                loadpayment(tokens);

            }, 3000);
                
               
        
        };
    
    </script>
@endpush