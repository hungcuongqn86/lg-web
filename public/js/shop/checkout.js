$(document).ready(function () {
	//select box
	$('.select-box').selectctr({css: {'width': '100%', 'height': '60px', 'margin': '10px 0', 'z-index': 'initial'}});
	
    //stripe response
    function stripeResponseHandler(status, response) {
        // Grab the form:
        var $form = $('#checkout-form');

        if (response.error) { // Problem!
            $('.loader').hide();
            $('.loader-modal').hide();
            // Show the errors on the form:
            //$('.alert-error').show();
            $('#p-alert-payment-error').show();
            $('#p-alert-payment-error').text(response.error.message);
            $(window).scrollTop(0);

        } else { // Token was created!
            // Get the token ID:
            var token = response.id;

            // Insert the token ID into the form so it gets submitted to the server:
            $form.append($('<input type="hidden" name="stripeToken">').val(token));

            // Submit the form:
            $form.get(0).submit();
        }
    };

    function validateEmail(email) {
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
        return emailReg.test(email);
    }

    if ($('#payment_card_type').val() == 'stripe') {
        document.getElementById('order-card-no').addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/[^\dA-Z]/g, '').replace(/(.{4})/g, '$1 ').trim();
        });

        Stripe.setPublishableKey('pk_test_B4jGbQHVh8QNKY54ZckXvzvE');
        var $form = $('#checkout-form');

        $('.btn-loading-progess').unbind('click');
        $('.submit-order').click(function (event) {
            var submit = true;
            if (!$.trim($('#order-email').val()) || !validateEmail($.trim($('#order-email').val()))) {
                submit = false;
                $('#order-email').parent().addClass('has-error');
                $('#p-alert-email').show();
            }
            else {
                $('#order-email').parent().removeClass('has-error');
                $('#p-alert-email').hide();
            }
            if (!$.trim($('#order-name').val())) {
                submit = false;
                $('#order-name').parent().addClass('has-error');
                $('#p-alert-name').show();
            }
            else {
                $('#order-name').parent().removeClass('has-error');
                $('#p-alert-name').hide();
            }
            if (!$.trim($('#order-address-line1').val())) {
                submit = false;
                $('#order-address-line1').parent().addClass('has-error');
                $('#p-alert-address').show();
            }
            else {
                $('#order-address-line1').parent().removeClass('has-error');
                $('#p-alert-address').hide();
            }
            if ($('#order-country').val() == 'US' && !$.trim($('#order-city').val())) {
                submit = false;
                $('#order-city').parent().addClass('has-error');
                $('#p-alert-city').show();
            }
            else {
                $('#order-city').parent().removeClass('has-error');
                $('#p-alert-city').hide();
            }
            if ($('#order-country').val() == 'US' && !$.trim($('#order-postal-code').val())) {
                submit = false;
                $('#order-postal-code').parent().addClass('has-error');
                $('#p-alert-postal-code').show();
            }
            else {
                $('#order-postal-code').parent().removeClass('has-error');
                $('#p-alert-postal-code').hide();
            }
            if ($('input:radio[name=payment-method]:checked').val() == 0) {
                if (!$.trim($('#order-card-no').val())) {
                    submit = false;
                    $('#order-card-no').parent().addClass('has-error');
                    $('#p-alert-card-no').show();
                }
                else {
                    $('#order-card-no').parent().removeClass('has-error');
                    $('#p-alert-card-no').hide();
                }
                if (!$.trim($('#order-card-mm').val())) {
                    submit = false;
                    $('#order-card-mm').parent().addClass('has-error');
                    $('#p-alert-card-mm').show();
                }
                else {
                    $('#order-card-mm').parent().removeClass('has-error');
                    $('#p-alert-card-mm').hide();
                }
                if (!$.trim($('#order-card-yy').val())) {
                    submit = false;
                    $('#order-card-yy').parent().addClass('has-error');
                    $('#p-alert-card-yy').show();
                }
                else {
                    $('#order-card-yy').parent().removeClass('has-error');
                    $('#p-alert-card-yy').hide();
                }
                if (!$.trim($('#order-card-cvc').val())) {
                    submit = false;
                    $('#order-card-cvc').parent().addClass('has-error');
                    $('#p-alert-card-cvc').show();
                }
                else {
                    $('#order-card-cvc').parent().removeClass('has-error');
                    $('#p-alert-card-cvc').hide();
                }
            }
            else {
                $('#order-card-no').parent().removeClass('has-error');
                $('#p-alert-card-no').hide();
                $('#order-card-mm').parent().removeClass('has-error');
                $('#p-alert-card-mm').hide();
                $('#order-card-yy').parent().removeClass('has-error');
                $('#p-alert-card-yy').hide();
                $('#order-card-cvc').parent().removeClass('has-error');
                $('#p-alert-card-cvc').hide();
            }

            if (submit == true) {
                $('.loader').show();
                $('.loader-modal').show();
                $(this).find('.loading-progess').css('width', '100%');
                if ($('input:radio[name=payment-method]:checked').val() == 0) {
                    event.preventDefault();
                    Stripe.card.createToken($form, stripeResponseHandler);
                }
            }
            else {
                event.preventDefault();
                //$('.alert-error').show();
                $(window).scrollTop(0);
            }
        });
    }
    else if ($('#payment_card_type').val() == 'braintree') {
        var $form = $('#checkout-form');
        braintree.client.create({
                authorization: $('#brain_tree_key').val()
            },
            function (err, clientInstance) {
                if (err) {
                    console.error(err);
                    return;
                }
                ;

                braintree.hostedFields.create({
                        client: clientInstance,
                        styles: {
                            'input': {
                                'font-size': '14px',
                                'font-family': 'helvetica, tahoma, calibri, sans-serif',
                                'color': '#3a3a3a'
                            },
                            ':focus': {
                                'color': 'black'
                            }
                        },
                        fields: {
                            number: {
                                selector: '#order-card-no',
                                placeholder: 'Card Number'
                            },
                            cvv: {
                                selector: '#order-card-cvc',
                                placeholder: 'CVC'
                            },
                            expirationMonth: {
                                selector: '#order-card-mm',
                                placeholder: 'MM'
                            },
                            expirationYear: {
                                selector: '#order-card-yy',
                                placeholder: 'YYYY'
                            }
                        }
                    },
                    function (err, hostedFieldsInstance) {
                        if (err) {
                            console.error(err);
                            return;
                        }

                        hostedFieldsInstance.on('validityChange', function (event) {
                            var field = event.fields[event.emittedBy];

                            if (field.isValid) {
                                if (event.emittedBy === 'expirationMonth' || event.emittedBy === 'expirationYear') {
                                    if (!event.fields.expirationMonth.isValid || !event.fields.expirationYear.isValid) {
                                        return;
                                    }
                                } else if (event.emittedBy === 'number') {
                                    $('#order-card-no').next('span').text('');
                                }

                                // Apply styling for a valid field
                                $(field.container).parents('.form-group').addClass('has-success');
                            } else if (field.isPotentiallyValid) {
                                // Remove styling  from potentially valid fields
                                $(field.container).parents('.form-group').removeClass('has-warning');
                                $(field.container).parents('.form-group').removeClass('has-success');
                                if (event.emittedBy === 'number') {
                                    $('#order-card-no').next('span').text('');
                                }
                            } else {
                                // Add styling to invalid fields
                                $(field.container).parents('.form-group').addClass('has-warning');
                                // Add helper text for an invalid card number
                                if (event.emittedBy === 'number') {
                                    $('#order-card-no').next('span').text('Looks like this card number has an error.');
                                }
                            }
                        });

                        $('.btn-loading-progess').unbind('click');
                        $('.submit-order').click(function (event) {
                            var submit = true;
                            if (!$.trim($('#order-email').val()) || !validateEmail($.trim($('#order-email').val()))) {
                                submit = false;
                                $('#order-email').parent().addClass('has-error');
                                $('#p-alert-email').show();
                            }
                            else {
                                $('#order-email').parent().removeClass('has-error');
                                $('#p-alert-email').hide();
                            }
                            if (!$.trim($('#order-name').val())) {
                                submit = false;
                                $('#order-name').parent().addClass('has-error');
                                $('#p-alert-name').show();
                            }
                            else {
                                $('#order-name').parent().removeClass('has-error');
                                $('#p-alert-name').hide();
                            }
                            if (!$.trim($('#order-address-line1').val())) {
                                submit = false;
                                $('#order-address-line1').parent().addClass('has-error');
                                $('#p-alert-address').show();
                            }
                            else {
                                $('#order-address-line1').parent().removeClass('has-error');
                                $('#p-alert-address').hide();
                            }
                            if ($('#order-country').val() == 'US' && !$.trim($('#order-city').val())) {
                                submit = false;
                                $('#order-city').parent().addClass('has-error');
                                $('#p-alert-city').show();
                            }
                            else {
                                $('#order-city').parent().removeClass('has-error');
                                $('#p-alert-city').hide();
                            }
                            if ($('#order-country').val() == 'US' && !$.trim($('#order-postal-code').val())) {
                                submit = false;
                                $('#order-postal-code').parent().addClass('has-error');
                                $('#p-alert-postal-code').show();
                            }
                            else {
                                $('#order-postal-code').parent().removeClass('has-error');
                                $('#p-alert-postal-code').hide();
                            }

                            if (submit == true) {
                                $('.loader').show();
                                $('.loader-modal').show();
                                $(this).find('.loading-progess').css('width', '100%');
                                if ($('input:radio[name=payment-method]:checked').val() == 0) {
                                    event.preventDefault();
                                    hostedFieldsInstance.tokenize(function (err, payload) {
                                        if (err) { // Problem!
                                            $('.loader').hide();
                                            $('.loader-modal').hide();
                                            // Show the errors on the form:
                                            //$('.alert-error').show();
                                            $('#p-alert-payment-error').show();
                                            $('#p-alert-payment-error').text(err.message);
                                            $(window).scrollTop(0);

                                        } else { // Token was created!
                                            // Get the token ID:
                                            var token = payload.nonce;

                                            // Insert the token ID into the form so it gets submitted to the server:
                                            $form.append($('<input type="hidden" name="stripeToken">').val(token));

                                            // Submit the form:
                                            $form.get(0).submit();
                                        }
                                    });
                                }
                            }
                            else {
                                event.preventDefault();
                                //$('.alert-error').show();
                                $(window).scrollTop(0);
                            }
                        });
                    });
            });
    }

    $('#order-country').change(function () {
        $('.loader').show();
        $('.loader-modal').show();
        $('#checkout-form').submit();
    });

    $('input:radio[name=payment-method]').change(function () {
        if ($(this).val() == 0) {
            $('.payment-cart').show(500);
            $('.submit-order-cart').show();
            $('.submit-order-paypal').hide();
        }
        else {
            $('.payment-cart').hide(500);
            $('.submit-order-cart').hide();
            $('.submit-order-paypal').show();
        }
    });
});