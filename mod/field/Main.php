<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\field;

class Main {

	private static $curform=null;
	private static $curfield=null;

	public static function smartyBlock_FieldForm($params, $content, $smarty, &$repeat) {
		if ($content === NULL) { // open tag
			if (self::$curform !== NULL) throw new \Exception("FieldForm don't support nested FieldForms");
			self::$curform=new FieldForm();
		} else { // close tag
			self::$curform=NULL;
			return "<form method='POST' action='SITEURL/fieldform/".$params['hook_on_post']."'>".$content."</form>";
		}
  }

  public static function smartyBlock_Field($params, $content, $smarty, &$repeat) {
		if ($content === NULL) { // open tag
			if (self::$curfield !== NULL) throw new \Exception("Field don't support nested Fields (but I have to support it for radios, select, ...)");
			$classname=$params['phpclass'];
			$field = new $classname($params['name'], $params['value']);
			self::$curfield=$field;
			if (self::$curform !== NULL) self::$curform->addField($field);
		} else { // close tag
			$field=self::$curfield;
			self::$curfield=NULL;
			return $field->render();
		}
  }

  public static function smartyFunction_FieldValidation($params, $template) {
		if (self::$curfield === NULL) throw new \Exception("FieldVerification must be inside a Field block");
		self::$curfield->addVerification($params['regexp'], $params['message']);
  }

	public static function hook_mod_field_post($hookname, $userdata, $urlparams) {
		\core\Hook::call('mod_field_post_'.$urlparams[1], $_POST);
	}

}

class FieldForm {
	private $fields=array();
	
	public function addField($field) {
		$this->fields[]=$field;
	}

	public function sqlinsert($table) {
		$vals=array();
		$a='';
		$b='';
		foreach($this->fields as $field) {
			$a.=($a == '' ? '' : ',').'`'.$field->name.'`';
			$b.=($b == '' ? '' : ',').'?';
			$vals[]=$field->getValue();
		}

		$q="INSERT INTO `$table` ($a) ($b)";
		\core\Core::$db->query($q, $vals);

		return \core\Core::$db->Insert_ID();
	}

	public function sqlupdate($table, $id) {
		$vals=array();
		$a='';
		foreach($this->fields as $field) {
			$a.=($a == '' ? '' : ',').'`'.$field->name.'`=?';
			$vals[]=$field->getValue();
		}

		$q="UPDATE `$table` SET $a WHERE `id`=?";
		$vals[]=$id;
		\core\Core::$db->query($q, $vals);

		return \core\Core::$db->Insert_ID();
	}
}

class Verification {
	public $regexp;
	public $message;
	public $fatal;
	public function __construct($regexp, $message, $fatal=true) {
		$this->regexp=$regexp;
		$this->message=$message;
	}
}

class Element {
	public $name;
	public $value;
	private $tests=array();

	public function __construct($name, $default='') {
		$this->name=$name;
		$this->value=$default;
	}

	public function addVerification($regexp, $message) {
		$this->tests[]=new Verification($regexp, $message);
	}

	public function test($value) {
		$result=array();
		foreach($this->tests as $test) {
			if (preg_match($test->regexp, $value)) $result[]=$test->message;
		}
		return $result;
	}

	public function getValue() {
		return $_REQUEST[$this->name];
	}
}

class Text extends Element {
	public function render() {
		return sprintf("<input type='text' name='%s' value='%s'/>",
									 $this->name, $this->value
									 );
	}
}

class Hidden extends Element {
	public function render() {
		return sprintf("<input type='hidden' name='%s' value='%s'/>",
									 $this->name, $this->value
									 );
	}
}

class RadioGroup extends Element {
	private $radios=array();
	
	public function addRadio($radio) {
		$this->radios[]=$radio;
	}

	public function render() {
	}
}

class Radio extends Element {
	public function render() {
		return sprintf("<input type='radio' name='%s' value='%s'/>",
									 $this->name, $this->value
									 );
	}
}

class Checkbox extends Element {
}

class Password extends Element {
}

class Textarea extends Element {
}

class Select extends Element {
}

class Submit extends Element {
	public function render() {
		return sprintf("<input type='submit' name='%s' value='%s'/>",
									 $this->name, $this->value
									 );
	}
}

