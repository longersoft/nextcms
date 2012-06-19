<?php
require_once 'CronExpression/AbstractField.php';
require_once 'CronExpression/DayOfMonthField.php';

/**
 * Month field.  Allows: * , / -
 *
 * @author Michael Dowling <mtdowling@gmail.com>
 */
class CronExpression_MonthField extends CronExpression_AbstractField
{
	/**
	 * {@inheritdoc}
	 */
	public function isSatisfiedBy(DateTime $date, $value)
	{
		// Convert text month values to integers
		$value = strtr($value, array(
            'JAN' => 1,
            'FEB' => 2,
            'MAR' => 3,
            'APR' => 4,
            'MAY' => 5,
            'JUN' => 6,
            'JUL' => 7,
            'AUG' => 8,
            'SEP' => 9,
            'OCT' => 10,
            'NOV' => 11,
            'DEC' => 12
		));

		return $this->isSatisfied($date->format('m'), $value);
	}

	/**
	 * {@inheritdoc}
	 */
	public function increment(DateTime $date, $invert = false)
	{
		if ($invert) {
			// $date->sub(new DateInterval('P1M'));
			// To support PHP 5.2+
			$date->modify('-1 month');
			$date->setDate($date->format('Y'), $date->format('m'), CronExpression_DayOfMonthField::getLastDayOfMonth($date));
			$date->setTime(23, 59, 0);
		} else {
			// $date->add(new DateInterval('P1M'));
			// To support PHP 5.2+
			$date->modify('+1 month');
			$date->setDate($date->format('Y'), $date->format('m'), 1);
			$date->setTime(0, 0, 0);
		}

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate($value)
	{
		return (bool) preg_match('/[\*,\/\-0-9A-Z]+/', $value);
	}
}
