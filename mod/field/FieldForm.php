<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\field;

class FieldForm {
	private $smarty;
	private $id;
	private $params=array();
	private $fields=array();
	private $html;
	private $ajaxreplaceid=null;
	private static $validators=array();
	
	public function __construct($smarty, $id, $tpl,
															$options=array('hookonpost' => null, 'hookoninvalidpost' => null,
																						 'ajaxreplaceid' => null, 'sendjson' => null)) {
		$this->id=$id;
    $this->smarty=$smarty;
		$this->smarty->assign('fieldform', $this);
		$this->html=$this->smarty->fetch($tpl);
		$this->ajaxreplaceid=isset($options['ajaxreplaceid']) ? $options['ajaxreplaceid'] : false;
		$this->sendjson=isset($options['sendjson']) ? $options['sendjson'] : false;


		if ($this->isPosted())
			if ($this->isValid()) {
				if (isset($options['hookonpost']) && $options['hookonpost'] !== null)
					\core\Hook::call($options['hookonpost'], $this);
			} else {
				if (isset($options['hookoninvalidpost']) && $options['hookoninvalidpost'] !== null)
					\core\Hook::call($options['hookoninvalidpost'], $this);
			}
	}

	// used internaly by smarty functions
	public static function _addValidator($validator) {
		self::$validators[$validator->name]=$validator;
	}

	// used internaly by smarty functions
	public static function getValidator($name) {
		if (isset(self::$validators[$name])) return self::$validators[$name];
		else throw new \Exception("Validator '".$name."' not found");
	}

	public function setParam($param, $value) {
		$this->params[$param]=$value;
	}

	public function getHtml($webpage) {
		//\mod\cssjs\Main::addJs($webpage, '/mod/cssjs/js/mootools.js');
		//\mod\cssjs\Main::addJs($webpage, '/mod/cssjs/js/mootools-more-all.js');
		//\mod\cssjs\Main::addJs($webpage, '/mod/field/js/field.js');
		$js="<script>\n";
		$js.="myForm=document.id('".$this->id."');\n";
		$js.="var myFormValidator = new Form.Validator.Tips(myForm, { evaluateFieldsOnChange: true, warningPrefix: '', errorPrefix: '' });\n";
		foreach(self::$validators as $validator) {
			$js.=$validator->getMootoolsJs();
		}
		//$js.="myForm.validate();";
    if ($this->ajaxreplaceid) $js.="new Form.Request(myForm, $('".$this->ajaxreplaceid."'));\n";
		if ($this->sendjson) $js.="myForm.addEvent('submit', function() { myForm.send(); return false; });";
		$js.="</script>";
		return "<form ".$this->getParamsStr()." id='".$this->id."' method='POST' action=''><input type='hidden' name='field_fieldform_id' value='".$this->id."'/>".$this->html."</form>$js";
	}

	public function getField_byIndex($index) {
		return $this->fields[$index];
	}

	private function getParamsStr($exclude=array()) {
		$str='';
		foreach($this->params as $k => $v)
			if (!in_array($k, $exclude))
				$str.=($str ? ' ' : '').$k."=$v";
		return $str;
	}

	public function isPosted() {
		return isset($_POST) && isset($_POST['field_fieldform_id']) && $_POST['field_fieldform_id'] == $this->id;
	}

	public function isValid() {
		foreach($this->fields as $field) {
			$res=$field->validate($field->getValue());
			if (count($res)) return false;
		}
		return true;
	}

	public function getValue($fieldname) {
		foreach($this->fields as $field)
			if ($field->name == $fieldname) return $field->getValue();
		throw new \Exception("Field '".$fieldname."'not found");
	}

	public function addField($field) {
		$this->fields[]=$field;
	}

	public function sqlInsert() {
		$querys=array();
		foreach($this->fields as $field) {
			if (!isset($field->params['sqltable'])) continue;
			$sqltable=$field->params['sqltable'];
			if (!isset($querys[$sqltable]))
				$querys[$sqltable]=array('vals' => array(), 'a' => '', 'b' => '');
			$query=&$querys[$sqltable];

			$query['a'].=($query['a'] == '' ? '' : ',').'`'.$field->name.'`';
			$query['b'].=($query['b'] == '' ? '' : ',').'?';
			$query['vals'][]=$field->getValue();
		}

		if (!count($querys)) throw new \Exception("No columns found to insert, maybe you have omited to add sqltable args to you're fields");

		foreach($querys as $sqltable => $query) {
			$q="INSERT INTO `".$sqltable."` (".$query['a'].") VALUES (".$query['b'].")";
			\core\Core::$db->query($q, $query['vals']);
		}

		return \core\Core::$db->lastInsertId();
	}

	public function sqlUpdate($id) {
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

class Validator {
	public $name;
	public $regexp;
	public $message;
	public $inverted;
	public function __construct($name, $regexp, $message, $inverted) {
		$this->name=$name;
		$this->regexp=$regexp;
		$this->message=$message;
		$this->inverted=$inverted;
	}

	public function getMootoolsString() {
		return $this->name;
	}

	public function getMootoolsJs() {
		return '
Form.Validator.add("'.addslashes($this->name).'", {
    errorMsg: "'.addslashes($this->message).'",
    test: function(field) {
	return '.($this->inverted ? '' : '!').'field.get("value").test('.$this->regexp.');
    }
});
';
	}
}

class Element {
	public $name;
	public $value;
	public $params;
	private $validators=array();

	public function __construct($params, $form) {
		if (!isset($params['name']))
			throw new \Exception("Element must have a name parameter");
		$this->name=$params['name'];
		$this->value=isset($params['value']) ? $params['value'] : '';
		$this->params=$params;
		$this->form=$form;
		foreach($this->params as $paramname => $param) {
			switch($paramname) {
			case 'validators':
				foreach(explode(',',$param) as $vname)
					$this->addValidator(FieldForm::getValidator(trim($vname)));
				break;
			}
		}
		$this->form->addField($this);
	}

	public function addValidator($validator) {
		$this->validators[$validator->name]=$validator;
	}

	public function validate($value) {
		$result=array();
		foreach($this->validators as $validator) {
			$res=preg_match($validator->regexp, $value);
			if (($res && !$validator->inverted) || (!$res && $validator->inverted)) {
				$result[]=$validator->message;
			}
		}
		return $result;
	}

	public function validatetohtml($value) {
		$validators=$this->validate($value);
		$res='';
		foreach($validators as $validator)
			$res.="<span class='field_error'>$validator</span>";
		return $res;
	}

	public function getValue() {
    if ($this->form->isPosted()) return isset($_POST[$this->name]) ? $_POST[$this->name] : NULL;
		return isset($this->params['value']) ? $this->params['value'] : '';
	}

	public function getMootoolsValidatorsString() {
		$str='';
		foreach($this->validators as $validator) {
			$tmp=$validator->getMootoolsString();
			if ($tmp) $str.=($str ? ' ' : '').$tmp;
		}
		if ($str) $str='data-validators="'.$str.'"';
		return $str;
	}

	protected function getParamsStr($exclude=array()) {
		$str='';
		foreach($this->params as $k => $v)
			if (!in_array($k, $exclude) && !in_array($k, array('phpclass', 'sqltable', 'validators')))
				$str.=($str ? ' ' : '').$k."='$v'";
		return $str;
	}

	public function render_show() {
		return htmlspecialchars($value);
	}

	public function render_edit_pre() {
		return '';
	}
	public function render_edit_post() {
		return '';
	}
}

class Text extends Element {
	public function render_edit_pre() {
		return sprintf("<input %s type='text' name='%s' value='%s' ".$this->getMootoolsValidatorsString()."/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->getValue(), ENT_QUOTES)
									 );
	}
}

class Hidden extends Element {
	public function render_edit_pre() {
		return sprintf("<input %s type='hidden' name='%s' value='%s'/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->getValue(), ENT_QUOTES)
									 );
	}
}

class RadioGroup extends Element {
	private $radios=array();
	
	public function addRadio($radio) {
		$this->radios[]=$radio;
	}
}

class Radio extends Element {
	public function render_edit_pre() {
		return sprintf("<input %s type='radio' name='%s' value='%s'%s/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, $this->params['value'], $this->is_checked() ? ' checked' : ''
									 );
	}
	public function is_checked() {
		if (isset($_POST[$this->name]) && ($_POST[$this->name] == $this->params['value']))
			return true;
		if (isset($this->params['checked'])) return true;
		return false;
	}
}

class Checkbox extends Element {
	public function render_edit_pre() {
		return sprintf("<input %s type='checkbox' name='%s' value='%s'%s/>",
									 $this->getParamsStr(array('name','value','type','checked')),
									 $this->name, $this->params['value'], $this->is_checked() ? ' checked' : ''
									 );
	}
	public function is_checked() {
		if (isset($_POST[$this->name])) {
			if ($_POST[$this->name] == $this->params['value'])
				return true;
		} else {
			if (isset($this->params['checked']))
				return true;
		}
		return false;
	}
}

class Password extends Element {
	public function render_edit_pre() {
		return sprintf("<input %s type='password' name='%s' value='%s' ".$this->getMootoolsValidatorsString()."/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->getValue(), ENT_QUOTES)
									 );
	}
}

class Textarea extends Element {
	public function render_edit_pre() {
		return sprintf("<textarea %s name='%s'".$this->getMootoolsValidatorsString()."/>%s</textarea>",
									 $this->getParamsStr(array('name','value')),
									 $this->name, htmlspecialchars($this->getValue(), ENT_QUOTES)
									 );
	}
}

class Select extends Element {
	private $options=array();
	
	public function addOption($option) {
		$this->options[]=$option;
	}

	public function render_edit_pre() {
		return sprintf("<select %s name='%s' ".$this->getMootoolsValidatorsString()."/>",
									 $this->getParamsStr(array('name','value')),
									 $this->name
									 );
	}

	public function render_edit_post() {
		return "</select>";
	}
}

Class SelectOption extends Element {
	public function render_edit_pre() {
		return sprintf("<option %s value='%s'%s/>%s</option>",
									 $this->getParamsStr(array('label','value','selected')),
									 $this->params['value'],
									 $this->is_selected() ? ' selected' : '',
									 isset($this->params['label']) ? $this->params['label'] : $this->value
									 );
	}

	public function is_selected() {
		if (isset($_POST[$this->name])) {
			if ($_POST[$this->name] == $this->params['value'])
				return true;
		} else {
			if (isset($this->params['selected']))
				return true;
		}
		return false;
	}
}

class Button extends Element {
	public function render_edit_pre() {
		return sprintf("<input %s type='button' name='%s' value='%s'/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->value, ENT_QUOTES)
									 );
	}
}

class Submit extends Element {
	public function render_edit_pre() {
		return sprintf("<input %s type='submit' name='%s' value='%s'/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->value, ENT_QUOTES)
									 );
	}
}

class File extends Element {
	public function __construct($params, $form) {
		parent::__construct($params, $form);
		$form->setParam('enctype', '"multipart/form-data"');
	}

	public function render_edit_pre() {
		$val=$this->getValue();
		return sprintf("<input %s type='file' name='%s' value='%s'/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, is_array($val) ? htmlspecialchars($val['name'], ENT_QUOTES) : ''
									 );
	}

	public function getValue() {
		return isset($_FILES[$this->name]) ? $_FILES[$this->name] : NULL;
	}
}
