<?php

namespace SwaggerGen\Swagger\Type;

/**
 * Basic decimal-point numeric type definition.
 *
 * @package    SwaggerGen
 * @author     Martijn van der Lee <martijn@vanderlee.com>
 * @copyright  2014-2015 Martijn van der Lee
 * @license    https://opensource.org/licenses/MIT MIT
 */
class NumberType extends AbstractType
{

	const REGEX_RANGE = '(?:([[<])(-?(?:\\d*\\.?\\d+|\\d+\\.\\d*))?,(-?(?:\\d*\\.?\\d+|\\d+\\.\\d*))?([\\]>]))?';
	const REGEX_DEFAULT = '(?:=(-?(?:\\d*\\.?\\d+|\\d+\\.\\d*)))?';

	private static $formats = array(
		'float' => 'float',
		'double' => 'double',
	);
	private $format;
	//private $allowEmptyValue; // for query/formData
	private $default = null;
	private $maximum = null;
	private $exclusiveMaximum = null;
	private $minimum = null;
	private $exclusiveMinimum = null;
	private $enum = array();
	private $multipleOf = null;

	protected function parseDefinition($definition)
	{
		$match = array();
		if (preg_match(self::REGEX_START . self::REGEX_FORMAT . self::REGEX_RANGE . self::REGEX_DEFAULT . self::REGEX_END, $definition, $match) !== 1) {
			throw new \SwaggerGen\Exception("Unparseable number definition: '{$definition}'");
		}

		if (!isset(self::$formats[strtolower($match[1])])) {
			throw new \SwaggerGen\Exception("Not a number: '{$definition}'");
		}
		$this->format = self::$formats[strtolower($match[1])];

		if (!empty($match[2])) {
			if ($match[3] === '' && $match[4] === '') {
				throw new \SwaggerGen\Exception("Empty number range: '{$definition}'");
			}

			$this->exclusiveMinimum = isset($match[2]) ? ($match[2] == '<') : null;
			$this->minimum = $match[3] === '' ? null : doubleval($match[3]);
			$this->maximum = $match[4] === '' ? null : doubleval($match[4]);
			$this->exclusiveMaximum = isset($match[5]) ? ($match[5] == '>') : null;
			if ($this->minimum && $this->maximum && $this->minimum > $this->maximum) {
				self::swap($this->minimum, $this->maximum);
				self::swap($this->exclusiveMinimum, $this->exclusiveMaximum);
			}
		}

		$this->default = isset($match[6]) && $match[6] !== '' ? $this->validateDefault($match[6]) : null;
	}

	public function handleCommand($command, $data = null)
	{
		switch (strtolower($command)) {
			case 'default':
				$this->default = $this->validateDefault($data);
				return $this;

			case 'enum':
				$words = self::wordSplit($data);
				foreach ($words as &$word) {
					$word = $this->validateDefault($word);
				}
				$this->enum = array_merge($this->enum, $words);
				return $this;

			case 'step':
				if (($step = doubleval($data)) > 0) {
					$this->multipleOf = $step;
				}
				return $this;
		}

		return parent::handleCommand($command, $data);
	}

	public function toArray()
	{
		return self::arrayFilterNull(array(
					'type' => 'number',
					'format' => $this->format,
					'default' => $this->default,
					'minimum' => $this->minimum,
					'exclusiveMinimum' => $this->exclusiveMinimum ? true : null,
					'maximum' => $this->maximum,
					'exclusiveMaximum' => $this->exclusiveMaximum ? true : null,
					'enum' => $this->enum,
					'multipleOf' => $this->multipleOf,
		));
	}

	public function __toString()
	{
		return __CLASS__;
	}

	private function validateDefault($value)
	{
		if (preg_match('~^-?(?:\\d*\\.?\\d+|\\d+\\.\\d*)$~', $value) !== 1) {
			throw new \SwaggerGen\Exception("Invalid number default: '{$value}'");
		}

		if ($this->maximum) {
			if (($value > $this->maximum) || ($this->exclusiveMaximum && $value == $this->maximum)) {
				throw new \SwaggerGen\Exception("Default number beyond maximum: '{$value}'");
			}
		}
		if ($this->minimum) {
			if (($value < $this->minimum) || ($this->exclusiveMinimum && $value == $this->minimum)) {
				throw new \SwaggerGen\Exception("Default number beyond minimum: '{$value}'");
			}
		}

		return doubleval($value);
	}

}
