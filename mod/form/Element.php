<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\form;

class Element {
	public $name;
	public $value;
	public $definedValue;
	public $params;
	private $validators=array();

	public function __construct($params, $form=NULL) {
		if ($params['type'] != 'option') {
			if (!isset($params['name']))
				throw new \Exception("Element must have a name parameter");
			else
				$this->name=$params['name'];
		}
		if ($params['type']) {
			$this->value=((isset($params['value'])) ? $params['value'] : '');
			$this->definedValue=((isset($params['definedValue'])) ? $params['definedValue'] : '');
		}
		$this->params=$params;
		$this->form=$form;
	}

	public function validate() {
		$errors = array();
		if (!isset($this->params['validators'])) return true;
		foreach($this->params['validators'] as $validator) {
			$test = $check = '';
			$validator =  (preg_replace(array('/ ?: ?/', '/-/'),array(':', '_'),$validator));
			if (strstr($validator,':')) {
				$os = preg_split('/:/', $validator);
				$method = $os[0];
				$check = $os[1];
			} else {
				$method = $validator;
			}
			if (!method_exists('\\mod\\form\\Validator', $method)) {
				$test = \mod\form\Validator::customValidate($method, $this->getValue());
			} else {
				if (!empty($check)) {
					$test = \mod\form\Validator::$method($this->getValue(), $check);
				} else {
					$test = \mod\form\Validator::$method($this->getValue());
				}
			}
			if ($test) {
					$errors[] = $test;
			}
		}
		return (sizeof($errors) > 0) ? $errors : true;
	}

	public function getValue() {
		if (isset($_REQUEST[$this->name]))
			return $_REQUEST[$this->name];
		else if (isset($this->definedValue))
			return $this->definedValue;
		else if (isset($this->params['value']))
			return $this->params['value'];
		else return '';
	}

	public function getMootoolsValidatorsString() {
		$str='';
		if (!isset($this->params['validators'])) return '';
		foreach($this->params['validators'] as $validator) {
			if (empty($str))
				$str='data-validators="'.$validator;
			else
				$str.=' '.$validator; 
		}
		return $str.'"';
	}

	protected function getParamsStr($exclude=array()) {
		$str='';
		foreach($this->params as $k => $v)
			if (!in_array($k, $exclude) && !in_array($k, array('validators', 'children')))
				$str.=($str ? ' ' : '').$k."='$v'";
		return $str;
	}

	public function render() {
		return '';
	}
}

class Text extends Element {
	public function render() {
		return sprintf("<input %s type='text' name='%s' value='%s' ".$this->getMootoolsValidatorsString()."/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->getValue(), ENT_QUOTES)
									 );
	}
}

class Hidden extends Element {
	public function render() {
		return sprintf("<input %s type='hidden' name='%s' value='%s'/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->getValue(), ENT_QUOTES)
									 );
	}
}

class Radio extends Element {
	public function render() {
		return sprintf("<input %s type='radio' name='%s' value='%s'%s/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, $this->params['value'], $this->is_checked() ? ' checked' : ''
									 );
	}
	public function is_checked() {
		if ( (isset($_REQUEST[$this->name]) && $_REQUEST[$this->name] == $this->params['value']) || (isset($this->definedValue) && $this->definedValue == $this->params['value']) )
			return true;
		if (isset($this->params['checked'])) 
			return true;
		return false;
	}
}

class Checkbox extends Element {
	public function render() {
		return sprintf("<input %s type='checkbox' name='%s' value='%s'%s/>",
									 $this->getParamsStr(array('name','value','type','checked')),
									 $this->name, $this->params['value'], $this->is_checked() ? ' checked' : ''
									 );
	}
	public function is_checked() {
		if ( (isset($_REQUEST[$this->name]) && $_REQUEST[$this->name] == $this->params['value']) || (isset($this->definedValue) && $this->definedValue == $this->params['value']) )
				return true;
		if (isset($this->params['checked']))
			return true;
		return false;
	}
}

class Password extends Element {
	public function render() {
		return sprintf("<input %s type='password' name='%s' value='%s' ".$this->getMootoolsValidatorsString()."/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->getValue(), ENT_QUOTES)
									 );
	}
}

class Textarea extends Element {
	public function render() {
		return sprintf("<textarea %s name='%s'".$this->getMootoolsValidatorsString()."/>%s</textarea>",
									 $this->getParamsStr(array('name','value')),
									 $this->name, htmlspecialchars($this->getValue(), ENT_QUOTES)
									 );
	}
}

class Select extends Element {
	private $_options=array();

	function __construct($params, $form) {
		parent::__construct($params, $form);
		if (isset($this->params['children']) && $this->params['children'] > 0)
			foreach($this->params['children'] as $child) {
				$this->addOption($child);
			}
	}
	
	public function addOption($option) {
		$option['parent'] = $this->params['name'];
		if (isset($this->params['definedValue']))
			$option['parentDefinedValue'] = $this->params['definedValue'];
		$this->_options[]=new Option($option);
	}

	public function render() {
		$outp = sprintf("<select %s name='%s' ".$this->getMootoolsValidatorsString().">",
									 $this->getParamsStr(array('name','value')),
									 $this->name
									 );
		if (isset($this->params['children']) && $this->params['children'] > 0)
			foreach($this->_options as $option) {
				$outp .= $option->render();
			}

		return $outp.'</select>';
	}
}

class Option extends Element {
	public function render() {
		return sprintf("<option %s value='%s'%s/>%s</option>",
									 $this->getParamsStr(array('label','value','selected', 'parent')),
									 $this->params['value'],
									 ($this->is_selected($this->params['parent'])) ? ' selected' : '',
									 (isset($this->params['label'])) ? $this->params['label'] : $this->value
									 );
	}

	public function is_selected($parentName) {
		if ((isset($_REQUEST[$parentName]) && $_REQUEST[$parentName] == $this->params['value']) || (isset($this->params['parentDefinedValue']) && $this->params['parentDefinedValue'] == $this->params['value']) )
			return true;
		if (isset($this->params['selected']))
				return true;
		return false;
	}
}

class Button extends Element {
	public function render() {
		return sprintf("<input %s type='button' name='%s' value='%s'/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->value, ENT_QUOTES)
									 );
	}
}

class Submit extends Element {
	public function render() {
		return sprintf("<input %s type='submit' name='%s' value='%s'/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, htmlspecialchars($this->value, ENT_QUOTES)
									 );
	}
}

class File extends Element {
	public function __construct($params, $form) {
		parent::__construct($params, $form);
	}

	public function render() {
		$val=$this->getValue();
		return sprintf("<input %s type='file' name='%s' value='%s' ".$this->getMootoolsValidatorsString()."/>",
									 $this->getParamsStr(array('name','value','type')),
									 $this->name, is_array($val) ? htmlspecialchars($val['name'], ENT_QUOTES) : ''
									 );
	}

	public function getValue() {
		return isset($_FILES[$this->name]) ? $_FILES[$this->name] : NULL;
	}
}
