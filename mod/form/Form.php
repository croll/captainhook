<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\form;

require_once(dirname(__FILE__).'/Element.php');

class Form {
	private $_id='';
	private $_file='';
	private $_smarty=NULL;
	private $_validators=array();
	private $_formValidatorOptions=array();
	private $_formValidatorDefaultOptions=array('evaluateFieldsOnChange' => false, 'evaluateFieldsOnBlur' => false, 'warningPrefix' => '', 'errorPrefix' => '');
	private $_datas=array();
	private $_defaultValues=array();
	private $_errors=array();
	private $_isJson=false;
	
	public function __construct($params, $smarty=NULL) {
		$this->_file = $params['file'];
		if (!is_null($smarty))
			$this->_smarty = $smarty;
		$file = CH_MODDIR.'/'.$params['mod'].'/'.$params['file'];
		if (!is_file($file) || !is_readable($file)) {
			throw new \Exception('json file not found or is not readdable');
		}
		if (isset($params['defaultValues'])) {
			$this->_defaultValues = $params['defaultValues'];
		}
		try {
			$this->_datas = $this->_myjson_decode(trim(file_get_contents($file)));
		} catch (\Exception $e) {
			\core\Core::log($e->getMessage());
		}
		if (!isset($this->_datas['id']))
			throw new \Exception('Form ID is not set.');
		$this->_id = $this->_datas['id'];
		if (isset($this->_datas['json']) && ($this->_datas['json'])) {
			$this->_isJson = true;
		}
		$this->_formValidatorOptions = $this->_datas['formValidatorOptions'];
		$this->_processElements($this->_datas['elements']);
	}

	private function _myjson_decode($json) {
		$datas = json_decode($json, true);
		switch (json_last_error()) {
			case JSON_ERROR_NONE:
				$msg = '';
				break;
			case JSON_ERROR_DEPTH:
				$msg = 'Maximum stack depth exceeded';
				break;
			case JSON_ERROR_STATE_MISMATCH:
				$msg = 'Underflow or the modes mismatch';
				break;
			case JSON_ERROR_CTRL_CHAR:
				$msg = 'Unexpected control character found';
				break;
			case JSON_ERROR_SYNTAX:
				$msg = 'Syntax error, malformed JSON';
				break;
			case JSON_ERROR_UTF8:
				$msg = 'Malformed UTF-8 characters, possibly incorrectly encoded';
				break;
			default:
				$msg = 'Unknown error';
				break;
		}

		if (!empty($msg))
			throw new \Exception($msg);

		return $datas;
	}

	public function getId() {
		return $this->_id;
	}

	public function render($content) {
		// Check integrity
		if (!isset($this->_datas['elements']) || !is_array($this->_datas['elements'])) {
			throw new \Exception('form does not contain any element');
		}
		// Form tag
		$outp = '<form';
		foreach(array('name', 'id', 'action', 'method', 'enctype', 'style', 'class') as $p) {
			if (isset($this->_datas[$p])) $outp .= " $p=\"".$this->_datas[$p]."\"";
		}
		$outp .= ">$content</form>";
		// JS
		$formJsObj = 'chForm_'.$this->_id;
		$jsOutp="var $formJsObj=document.id('".$this->_id."');\n";
		if (!isset($this->_datas['formValidatorOptions']) || empty($this->_datas['formValidatorOptions'])) {
			$jsOutp.="var ${formJsObj}Validator = new Form.Validator.Inline($formJsObj, ".json_encode($this->_formValidatorDefaultOptions).");\n";
		} else {
			// HACK because jsonencode enclose everything with double quote (as it must be done) but it sucks with js function names
			$uglyOpts = array();
			$validatorOptions = array();
			foreach ($this->_datas['formValidatorOptions'] as $k=>$v) {
				if (preg_match('/on[A-Z]{1}[a-z]+Validate/', $k, $matches)) {
					$uglyOpts[] = '"'.$k.'":'.$v;
				} else {
					$validatorOptions[$k] = $v;
				}
			}
			$validatorOptions = array_merge($this->_formValidatorDefaultOptions, $validatorOptions);
			$uglyStr = str_replace('}', ','.implode(',', $uglyOpts).'}', json_encode($validatorOptions));
			$jsOutp.="var ${formJsObj}Validator = new Form.Validator.Inline($formJsObj, ".$uglyStr.");\n";
		}
		// Validators
		if (isset($this->_datas['customValidators'])) {
			foreach($this->_datas['customValidators'] as $validator) {
				\mod\form\Validator::addCustomValidator($validator['name'], $validator['regexp'], $validator['errorMsg']);
			}
			$jsOutp .= \mod\form\Validator::getMootoolsJs();
		}
		// End of JS
		return ($this->_isJson) ? $outp."<script>$jsOutp</script>" : $outp."<script>window.addEvent('domready', function() { $jsOutp })</script>";
	}

	public function assign() {
		$elements = array();
		foreach($this->fields as $field) {
			$elements[$field->name] = $field->render();
		}
		$this->_smarty->assign($this->_id, $elements);
		$this->_smarty->assign('ch_form_'.$this->_id, $this);
	}

	private function _processElements($elements) {
		foreach($elements as $el) {
			$className = '\\mod\\form\\'.ucfirst($el['type']);
			try {
				if (class_exists($className)) {
					try {
						if (isset($this->_defaultValues[$el['name']])) {
							$el['definedValue'] = $this->_defaultValues[$el['name']];
						}
						$this->fields[$el['name']] = new $className($el, $this);
					} catch (\Exception $e) {
						throw new \Exception($e->getMessage());
					}
				}
			} catch (\Exception $e) {
				throw new \Exception($e->getMessage());
			}
		}
	}

	public function setDefaultValues($datas) {
		$this->_defaultValues = $datas;
	}

	public function getValue($name) {
		if (!isset($this->fields[$name]))
			throw new \Exception("Field $name not found");
		else return $this->fields[$name]->getValue();
	}

	public function getFieldValues() {
		$values = array();
		foreach($this->fields as $field) {
			$values[$field->name] = $field->getValue();
		}
		return $values;
	}

	public function validate() {
		$this->_errors = array();
		foreach($this->fields as $field) {
			$fv = $field->validate();
			if ($fv !== true) $this->_errors[$field->name] = $fv;
		}
		return (sizeof($this->_errors)) ? false : true;
	}

	public function getValidationErrors() {
		return $this->_errors;
	}

}
