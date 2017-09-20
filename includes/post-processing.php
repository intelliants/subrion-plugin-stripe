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

if (isset($_POST['stripeToken'])) {
    $iaView->set('nocsrf', true);

    $iaStripe = $iaCore->factoryModule('stripe', 'stripe', 'common');

    $iaStripe->load();

    $error = true;

    try {
        $plan = $temp_transaction['plan_id'] ? $iaCore->factory('plan')->getById($temp_transaction['plan_id']) : null;

        if ($plan) {
            $planName = 'subrion_plan_' . $plan['id'];

            $iaStripe->createPlan($planName, $plan, $temp_transaction);

            $email = empty($temp_transaction['email'])
                ? iaUsers::getIdentity()->email
                : $temp_transaction['email'];

            $iaStripe->createCustomer($_POST['stripeToken'], $planName, $email);
        } else {
            $iaStripe->createCharge($_POST['stripeToken'], $temp_transaction['amount'], $temp_transaction['currency']);
        }

        $transaction['id'] = $temp_transaction['id'];
        $transaction['status'] = iaTransaction::PASSED;

        $payer = explode(' ', iaUsers::getIdentity()->fullname);

        $order = [
            'payment_gross' => (float)$temp_transaction['amount'],
            'mc_currency' => $temp_transaction['currency'],
            'payment_date' => date(iaDb::DATETIME_SHORT_FORMAT),
            'payment_status' => iaLanguage::get(iaTransaction::PASSED),
            'first_name' => iaSanitize::html($payer[0]),
            'last_name' => isset($payer[1]) ? iaSanitize::html($payer[1]) : '',
            'payer_email' => isset($email) ? $email : '',
            'txn_id' => iaSanitize::html($_POST['stripeToken'])
        ];

        $error = false;
    } catch (Exception $e) {
        $messages[] = 'Stripe API error: ' . $e->getMessage();
    }
}