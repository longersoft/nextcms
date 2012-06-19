<?php
require_once 'CronExpression/AbstractField.php';

/**
 * Year field.  Allows: * , / -
 *
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class CronExpression_YearField extends CronExpression_AbstractField
{
	/**
	 * {@inheritdoc}
	 */
	public function isSatisfiedBy(DateTime $date, $value)
	{
		return $this->isSatisfied($date->format('Y'), $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function increment(DateTime $date, $invert = false)
	{
		if ($invert) {
			// $date->sub(new DateInterval('P1Y'));
			// To support PHP 5.2+
			$date->modify('-1 year');
			$date->setDate($date->format('Y'), 12, 31);
			$date->setTime(23, 59, 0);
		} else {
			// $date->add(new DateInterval('P1Y'));
			// To support PHP 5.2+
			$date->modify('+1 year');
			$date->setDate($date->format('Y'), 1, 1);
			$date->setTime(0, 0, 0);
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
