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

if (isset($_POST['stripeToken']))
{
	$iaStripe = $iaCore->factoryPlugin('stripe', 'common');

	$iaStripe->load();

	$error = true;

	try {
		$plan = $temp_transaction['plan_id'] ? $iaCore->factory('plan')->getById($temp_transaction['plan_id']) : null;

		if ($plan)
		{
			$planName = 'subrion_plan_' . $plan['id'];

			$stripePlan = array(
				'amount' => (int)$temp_transaction['amount'],
				'interval' => $plan['unit'],
				'name' => $plan['title'],
				'currency' => $temp_transaction['currency'],
				'id' => $planName
			);

			if (isset($plan['cycles']) && $plan['cycles'] != 0)
			{
				$stripePlan['quantity'] = $plan['cycles'];
			}

			\Stripe\Plan::create($stripePlan);

			$email = empty($temp_transaction['email']) ? iaUsers::getIdentity()->email : $temp_transaction['email'];

			$customer = \Stripe\Customer::create(array(
				'source' => $_POST['stripeToken'],
				'plan' => $planName,
				'email' => $email
			));
		}
		else
		{
			\Stripe\Charge::create(array(
				'amount' => $temp_transaction['amount'],
				'currency' => $temp_transaction['currency'],
				'card' => $_POST['stripeToken']
			));
		}

		$transaction['status'] = iaTransaction::PASSED;

		$payer = explode(' ', iaUsers::getIdentity()->fullname);

		$order = array(
			'payment_gross' => (float)$temp_transaction['amount'],
			'mc_currency' => $temp_transaction['currency'],
			'payment_date' => date(iaDb::DATETIME_SHORT_FORMAT),
			'payment_status' => iaLanguage::get(iaTransaction::PASSED),
			'first_name' => iaSanitize::html($payer[0]),
			'last_name' => isset($payer[1]) ? iaSanitize::html($payer[1]) : '',
			'payer_email' => '',
			'txn_id' => iaSanitize::html($_POST['stripeToken'])
		);

		$error = false;
	}
	catch (Exception $e)
	{
		$messages[] = 'Stripe API error: ' . $e->getMessage();
	}
}