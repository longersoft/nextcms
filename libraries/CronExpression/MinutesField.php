<?php
require_once 'CronExpression/AbstractField.php';

/**
 * Minutes field.  Allows: * , / -
 *
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class CronExpression_MinutesField extends CronExpression_AbstractField
{
	/**
	 * {@inheritdoc}
	 */
	public function isSatisfiedBy(DateTime $date, $value)
	{
		return $this->isSatisfied($date->format('i'), $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function increment(DateTime $date, $invert = false)
	{
		if ($invert) {
			// $date->sub(new DateInterval('PT1M'));
			// To support PHP 5.2+
			$date->modify('-1 minute');
		} else {
			// $date->add(new DateInterval('PT1M'));
			// To support PHP 5.2+
			$date->modify('+1 minute');
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($value)
	{
		return (bool) preg_match('/[\*,\/\-0-9]+/', $value);
	}
}
