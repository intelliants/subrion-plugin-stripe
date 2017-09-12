<span class="payment-errors"></span>
<span class="payment-success"></span>

{ia_print_css files='_IA_URL_modules/stripe/templates/front/css/style'}
<form action="{$smarty.const.IA_URL}pay/{$transaction.sec_key}/completed/" method="post" id="payment-form">
	<div class="row">
		<div class="col-md-3">
			<div class="form-group">
				<label for="card-number">Card Number</label>
				<input type="text" size="20" autocomplete="off" class="card-number form-control" id="card-number">
			</div>
			<div class="form-group">
				<label for="card-cvc">CVC</label>
				<input type="text" size="4" autocomplete="off" class="card-cvc form-control" id="card-cvc">
			</div>
			<div class="form-group">
				<label>Expiration (MM/YYYY)</label>
				<div class="row">
					<div class="col-md-6">
						<input type="text" size="2" class="card-expiry-month form-control">
					</div>
					<span class="s-divider"> / </span>
					<div class="col-md-6">
						<input type="text" size="4" class="card-expiry-year form-control">
					</div>
				</div>
			</div>
			<input type="submit" name="data-stripe-info" class="submit-button btn btn-primary">
		</div>
	</div>
</form>

{ia_print_js files='https://js.stripe.com/v1/'}
{ia_add_js}

Stripe.setPublishableKey('{$key}');

function stripeResponseHandler(status, response)
{
	if (response.error)
	{
		$('.payment-errors').html(response.error.message);
		$('.submit-button').prop('disabled', false);
	}
	else
	{
		var $form = $("#payment-form");
		var token = response['id'];
		$form.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
		$form.get(0).submit();
	}
}
$(function()
{
	$('#payment-form').on('submit', function(e)
	{
		e.preventDefault();

		$('.submit-button').prop('disabled', true);
		Stripe.createToken(
		{
			number: $('.card-number').val(),
			cvc: $('.card-cvc').val(),
			exp_month: $('.card-expiry-month').val(),
			exp_year: $('.card-expiry-year').val()
		}, stripeResponseHandler);
	});
});
{/ia_add_js}