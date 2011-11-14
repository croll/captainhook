<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\field;

class FieldForm {
	private $smarty;
	private $uniquename;
	private $fields=array();
	private $html;
	
	public $curfield=array(); // used by smarty functions

	public function __construct($uniquename, $tpl) {
		$this->uniquename=$uniquename;
		$this->smarty=\mod\smarty\Main::newSmarty();
		$this->smarty->assign('fieldform', $this);
		$this->html=$this->smarty->fetch($tpl);
	}

	public function get_html() {
		return "<form method='POST'><input type='hidden' name='field_fieldform_uniquename' value='".$this->uniquename."'/>".$this->html."</form>";
	}

	public function isPosted() {
		return isset($_POST) && isset($_POST['field_fieldform_uniquename']) && $_POST['field_fieldform_uniquename'] == $this->uniquename;
	}

	public function isValid() {
		foreach($this->fields as $field) {
			$res=$field->test($field->getValue());
			if (count($res)) return false;
		}
		return true;
	}

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
	public $inverted;
	public function __construct($regexp, $message, $inverted) {
		$this->regexp=$regexp;
		$this->message=$message;
		$this->inverted=$inverted;
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

	public function addVerification($verification) {
		$this->tests[]=$verification;
	}

	public function test($value) {
		$result=array();
		foreach($this->tests as $test) {
			$res=preg_match($test->regexp, $value);
			if (($res && !$test->inverted) || (!$res && $test->inverted)) $result[]=$test->message;
		}
		return $result;
	}

	public function testtohtml($value) {
		$tests=$this->test($value);
		$res='';
		foreach($tests as $test)
			$res.="<span class='field_error'>$test</span>";
		return $res;
	}

	public function getValue() {
		return $_REQUEST[$this->name];
	}
}

class Text extends Element {
	public function render() {
		return sprintf("<input type='text' name='%s' value='%s'/>%s",
									 $this->name, $this->value, $this->testtohtml($this->value)
									 );
	}
}

class Hidden extends Element {
	public function render() {
		return sprintf("<input type='hidden' name='%s' value='%s'/>%s",
									 $this->name, $this->value, $this->testtohtml($this->value)
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

