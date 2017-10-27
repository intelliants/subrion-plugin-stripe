{ia_print_css files='_IA_URL_modules/stripe/templates/front/css/style'}
<div id="js-stripe-container">
    <div class="js-msg alert alert-warning" style="display: none"></div>

    <form action="{$smarty.const.IA_URL}pay/{$transaction.sec_key}/completed/" method="post" class="form-horizontal">
        {preventCsrf}
        <div class="form-group">
            <label class="col-sm-4 control-label" for="card-number">{lang key='card_number'}</label>
            <div class="col-sm-6">
                <input type="text" maxlength="20" autocomplete="off" class="js-card-number form-control" id="card-number">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label" for="card-cvc">{lang key='card_cvc'}</label>
            <div class="col-sm-2">
                <input type="text" maxlength="3" autocomplete="off" class="js-card-cvc form-control" id="card-cvc">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">{lang key='card_expiration'}</label>
            <div class="col-sm-6">
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" maxlength="2" class="js-card-expiry-month form-control">
                    </div>
                    <div class="col-md-1"> / </div>
                    <div class="col-md-6">
                        <input type="text" maxlength="4" class="js-card-expiry-year form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-4 col-md-2">
                <button type="submit" name="data-stripe-info" class="btn btn-primary">{lang key='proceed'}</button>
            </div>
        </div>
    </form>
</div>
{ia_print_js files='https://js.stripe.com/v1/'}
{ia_add_js}
$(function() {
    var $container = $('#js-stripe-container');

    Stripe.setPublishableKey('{$key}');

    $('form', $container).on('submit', function(e) {
        e.preventDefault();

        $('button[type="submit"]').prop('disabled', true);

        Stripe.createToken({
            number: $('.js-card-number', $container).val(),
            cvc: $('.js-card-cvc', $container).val(),
            exp_month: $('.js-card-expiry-month', $container).val(),
            exp_year: $('.js-card-expiry-year', $container).val()
        }, stripeResponseHandler);
    });

    function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.js-msg', $container).text(response.error.message).slideDown('fast');
            $('button[type="submit"]', $container).prop('disabled', false);
        } else {
            $('form', $container)
                .append('<input type="hidden" name="stripeToken" value="' + response.id + '">')
                .get(0).submit();
        }
    }
});
{/ia_add_js}