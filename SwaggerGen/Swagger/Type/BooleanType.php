<?php

namespace SwaggerGen\Swagger\Type;

/**
 * Basic boolean type definition.
 *
 * @package    SwaggerGen
 * @author     Martijn van der Lee <martijn@vanderlee.com>
 * @copyright  2014-2015 Martijn van der Lee
 * @license    https://opensource.org/licenses/MIT MIT
 */
class BooleanType extends AbstractType
{

	const REGEX_DEFAULT = '(?:=(true|false|1|0))?';

	//private $allowEmptyValue; // for query/formData
	private $default = null;

	protected function parseDefinition($definition)
	{
		$match = array();
		if (preg_match(self::REGEX_START . self::REGEX_FORMAT . self::REGEX_DEFAULT . self::REGEX_END, $definition, $match) !== 1) {
			throw new \SwaggerGen\Exception("Unparseable boolean definition: '{$definition}'");
		}

		if (strtolower($match[1]) !== 'boolean') {
			throw new \SwaggerGen\Exception("Not a boolean: '{$definition}'");
		}

		if (!empty($match[2])) {
			$this->default = ($match[2] == '1') || (strtolower($match[2]) === 'true');
		}
	}

	public function handleCommand($command, $data = null)
	{
		switch (strtolower($command)) {
			case 'default':
				$this->default = ($data == '1') || (strtolower($data) === 'true');
				return $this;
		}

		return parent::handleCommand($command, $data);
	}

	public function toArray()
	{
		$array = array(
			'type' => 'boolean',
		);

		if ($this->default !== null) {
			$array['default'] = $this->default;
		}

		return $array;
	}

	public function __toString()
	{
		return __CLASS__;
	}

}
