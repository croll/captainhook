<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\form;

class Validator {

	private static $_customValidators = array();

	public static function getMootoolsJs() {
		$outp = '';
		foreach(self::$_customValidators as $name=>$validator) {
			$outp .= 'Form.Validator.add("'.addslashes($name).'", {
				 errorMsg: "'.addslashes($validator['errorMsg']).'",
				 test: function(field) {
				 return field.get("value").test('.$validator['regexp'].');
				}});';
		}
		return $outp;
	}

	public static function addCustomValidator($name, $regexp, $errorMsg) {
		self::$_customValidators[$name] = array('regexp' => $regexp, 'errorMsg' => $errorMsg);
	}

	public static function customValidate($name, $value) {
		if (!isset(self::$_customValidators[$name])) 
			throw new \Exception("Custom validator $name does not exists");
		$validator = self::$_customValidators[$name];
		return (!preg_match($validator['regexp'], $value)) ? $validator['error'] : NULL;
	}

	public static function required($str) {
		return (!empty($str)) ? NULL : 'This field is required.';
	}

	public static function length($str, $value) {
		return ($str == '' || strlen($str) != $value) ? NULL : 'Please enter '.$value.' characters (you entered '.strlen($value).' characters)';
	}

	public static function minLength($str, $value) {
		return ($str == '' || strlen($str) >= $value) ? NULL : 'Please enter at least '.$value.' characters (you entered '.strlen($value).' characters)';
	}

	public static function maxLength($str, $value) {
		return ($str == '' || strlen($str) <= $value) ? NULL : 'Please enter no more than '.$value.' characters (you entered '.strlen($value).' characters)';
	}

	public static function validate_integer($str) {
		return ($str == '' || preg_match("/^[0-9]+$/", $str)) ? NULL : 'Please enter an integer in this field. Numbers with decimals (e.g. 1.25) are not permitted.';
	}

	public static function validate_numeric($str) {
		return ($str == '' || preg_match("/^[0-9]+\.?[0-9]*$/", $str)) ? NULL : 'Please enter an integer in this field. Numbers with decimals (e.g. 1.25) are not permitted.';
	}

	public static function validate_digits($str) {
		return ($str == '' || preg_match("/^[\d() .:\-\+#]+$/", $str)) ? NULL : 'Please use numbers and punctuation only in this field (for example, a phone number with dashes or dots is permitted).';
	}

	public static function validate_alpha($str) {
		return ($str == '' || preg_match("/^[a-zA-Z]+$/", $str)) ? NULL : 'Please use only letters (a-z) within this field. No spaces or other characters are allowed.';
	}

	public static function validate_alphanum($str) {
		return ($str == '' || preg_match("/^[a-zA-Z0-9]+$/", $str)) ? NULL : 'Please use only letters (a-z) or numbers (0-9) in this field. No spaces or other characters are allowed.';
	}

	public static function validate_date($str) {
		if ($str == '') return NULL;
	  if (substr_count($str, '/') == 2) {
	    list($d, $m, $y) = explode('/', $str);
			if (!checkdate($m, $d, sprintf('%04u', $y)))
				return 'Date invalid';
	  }
	  return NULL;
	}

	public static function validate_email($str) {
		return ($str == '' || preg_match("/^(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+\/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i", $str)) ? NULL : 'Please enter a valid email address. For example "fred@domain.com".';
	}

	public static function validate_url($str) {
		return ($str == '' || preg_match("/^(https?|ftp|rmtp|mms):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i", $str)) ? NULL : 'Please enter a valid URL such as http://www.example.com.';
	}

}
