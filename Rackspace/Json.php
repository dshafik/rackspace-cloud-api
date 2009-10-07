<?php
/**
 * Rackspace Json Helper
 * 
 * @package Rackspace
 * @subpackage Rackspace_Json
 */

/**
 * Rackspace_Json_Container
 */
require_once 'Rackspace/Json/Container.php';

/**
 * Rackspace_Json_Array
 */
require_once 'Rackspace/Json/Array.php';

/**
 * Rackspace_Json_Object
 */
require_once 'Rackspace/Json/Object.php';

/**
 * Rackspace_Json_Int
 */
require_once 'Rackspace/Json/Int.php';

/**
 * Rackspace_Json_String
 */
require_once 'Rackspace/Json/String.php';

/**
 * Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * Rackspace Json Helper
 *
 * @package Rackspace
 * @subpackage Rackspace_Json
 */
class Rackspace_Json {

	/**
	 * Retrieve value for JSON encoding
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	static public function getValue($value) {
		if ($value instanceof Rackspace_Json_Int) {
			return $value->toInt();
		}
		elseif ($value instanceof Rackspace_Json_String) {
			return $value->toString();
		}
		elseif ($value instanceof Rackspace_Json_Array) {
			return $value->toArray();
		}
		elseif ($value instanceof Rackspace_Json_Object) {
			return $value->toObject();
		}

		if (is_array($value)) {
			foreach ($value as $key => $v) {
				$value[$key] = self::getValue($v);
			}
		}

		return $value;
	}

	/**
	 * Indents a flat JSON string to make it more human-readable
	 *
	 * Originally taken from http://recurser.com/articles/2008/03/11/format-json-with-php/
	 * and modified to skip special characters in quoted keys and values.
	 *
	 * This may invalidate the JSON string and intended for debugging.
	 *
	 * @param string $json The original JSON string to process
	 * @return string Indented version of the original JSON string
	 */
	public static function indent($json) {

		$result    = '';
		$pos       = 0;
		$strLen    = strlen($json);
		$indentStr = '  ';
		$newLine   = "\n";

		for($i = 0; $i <= $strLen; $i++) {

			// Grab the next character in the string
			$char = substr($json, $i, 1);

			// Skip quoted keys and values
			if ($char == '"' || $char == "'") {
				$lookfor = $char;
				for ($i = $i+1; $i <= $strLen; $i++) {
					$result .= $char;
					$char = substr($json, $i, 1);
					if ($char == $lookfor) {
						break;
					}
				}
			}

			// If this character is the end of an element,
			// output a new line and indent the next line
			if($char == '}' || $char == ']') {
				$result .= $newLine;
				$pos --;
				for ($j=0; $j<$pos; $j++) {
					$result .= $indentStr;
				}
			}

			// Add the character to the result string
			$result .= $char;

			// If the last character was the beginning of an element,
			// output a new line and indent the next line
			if ($char == ',' || $char == '{' || $char == '[') {
				$result .= $newLine;
				if ($char == '{' || $char == '[') {
					$pos ++;
				}
				for ($j = 0; $j < $pos; $j++) {
					$result .= $indentStr;
				}
			}
		}

		return $result;
	}
}