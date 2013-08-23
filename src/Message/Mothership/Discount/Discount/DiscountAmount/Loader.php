<?php

namespace Message\Mothership\Discount\Discount\DiscountAmount;

use Message\Cog\DB\Query;
use Message\Cog\DB\Result;

class Loader
{
	protected $_query;

	public function __construct(Query $query)
	{
		$this->_query = $query;
	}

	public function getByDiscount($discount)
	{
		$result = $this->_query->run(
			'SELECT
				discount_id,
				locale,
				currency_id,
				amount
			FROM
				discount_amount
			WHERE
				discount_id  = ?i
		', 	array(
				$discount->id,
			)
		);

		$discountAmounts = $result->bindTo('Message\\Mothership\\Discount\\Discount\\DiscountAmount\\DiscountAmount');

		foreach ($result as $key => $data) {
			$discountAmounts[$key]->discount = $discount;
			$discountAmounts[$key]->amount = (float) $data->amount;

			// TODO Maybe load Locale-Object??
		}

		return $discountAmounts;
	}
}
