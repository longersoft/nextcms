<?php
require_once 'CronExpression/AbstractField.php';

/**
 * Hours field.  Allows: * , / -
 *
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class CronExpression_HoursField extends CronExpression_AbstractField
{
	/**
	 * {@inheritdoc}
	 */
	public function isSatisfiedBy(DateTime $date, $value)
	{
		return $this->isSatisfied($date->format('H'), $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function increment(DateTime $date, $invert = false)
	{
		if ($invert) {
			// $date->sub(new DateInterval('PT1H'));
			// To support PHP 5.2+
			$date->modify('-1 hour');
			$date->setTime($date->format('H'), 59, 0);
		} else {
			// $date->add(new DateInterval('PT1H'));
			// To support PHP 5.2+
			$date->modify('+1 hour');
			$date->setTime($date->format('H'), 0, 0);
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
