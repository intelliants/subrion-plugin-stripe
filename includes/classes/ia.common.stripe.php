<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2015 Intelliants, LLC <http://www.intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link http://www.subrion.org/
 *
 ******************************************************************************/

class iaStripe extends abstractCore
{
	protected $_isDemoMode;


	public function init()
	{
		parent::init();

		$this->_isDemoMode = (bool)$this->iaCore->get('stripe_demo_mode');
	}

	public function getCredential($public = true)
	{
		$type1 = $public ? 'pub' : 'priv';
		$type2 = $this->_isDemoMode ? 'demo' : 'live';

		$key = sprintf('stripe_key_%s_%s', $type1, $type2);

		return $this->iaCore->get($key);
	}

	public function load()
	{
		$basePath = IA_PLUGINS . 'stripe/includes/lib/';

		require $basePath . 'Stripe.php';

		require $basePath . 'Util/AutoPagingIterator.php';
		require $basePath . 'Util/RequestOptions.php';
		require $basePath . 'Util/Set.php';
		require $basePath . 'Util/Util.php';

		require $basePath . 'HttpClient/ClientInterface.php';
		require $basePath . 'HttpClient/CurlClient.php';

		require $basePath . 'Error/Base.php';
		require $basePath . 'Error/Api.php';
		require $basePath . 'Error/ApiConnection.php';
		require $basePath . 'Error/Authentication.php';
		require $basePath . 'Error/Card.php';
		require $basePath . 'Error/InvalidRequest.php';
		require $basePath . 'Error/RateLimit.php';

		require $basePath . 'ApiResponse.php';
		require $basePath . 'JsonSerializable.php';
		require $basePath . 'StripeObject.php';
		require $basePath . 'ApiRequestor.php';
		require $basePath . 'ApiResource.php';
		require $basePath . 'SingletonApiResource.php';
		require $basePath . 'AttachedObject.php';
		require $basePath . 'ExternalAccount.php';

		require $basePath . 'Account.php';
		require $basePath . 'AlipayAccount.php';
		require $basePath . 'ApplicationFee.php';
		require $basePath . 'ApplicationFeeRefund.php';
		require $basePath . 'Balance.php';
		require $basePath . 'BalanceTransaction.php';
		require $basePath . 'BankAccount.php';
		require $basePath . 'BitcoinReceiver.php';
		require $basePath . 'BitcoinTransaction.php';
		require $basePath . 'Card.php';
		require $basePath . 'Charge.php';
		require $basePath . 'Collection.php';
		require $basePath . 'CountrySpec.php';
		require $basePath . 'Coupon.php';
		require $basePath . 'Customer.php';
		require $basePath . 'Dispute.php';
		require $basePath . 'Event.php';
		require $basePath . 'FileUpload.php';
		require $basePath . 'Invoice.php';
		require $basePath . 'InvoiceItem.php';
		require $basePath . 'Order.php';
		require $basePath . 'Plan.php';
		require $basePath . 'Product.php';
		require $basePath . 'Recipient.php';
		require $basePath . 'Refund.php';
		require $basePath . 'SKU.php';
		require $basePath . 'Subscription.php';
		require $basePath . 'Token.php';
		require $basePath . 'Transfer.php';
		require $basePath . 'TransferReversal.php';

		\Stripe\Stripe::setApiKey($this->getCredential(false));
	}
}