<?php  //  -*- mode:php; tab-width:2; c-basic-offset:2; -*-

namespace mod\field;

class Main {

  public static function smartyFunction_Field($params, $template) {
		$fieldform = $template->smarty->tpl_vars['fieldform']->value;
		if (!isset($params['phpclass']))
			throw new \Exception("Field must have a phpclass");
		$classname=$params['phpclass'];
		$field = new $classname($params['name'], isset($params['value']) ? $params['value'] : '', $params);
		$fieldform->addField($field);
		return $field->render();
  }
	
  public static function smartyFunction_FieldValidator($params, $template) {
		if (!isset($params['name']))
			throw new \Exception("FieldValidator must have a 'name' parameter");
		if (!isset($params['message']))
			throw new \Exception("FieldValidator must have a 'message' parameter");

		if (isset($params['regexp'])) {
			$regexp=$params['regexp'];
			$inverted=false;
		} else if (isset($params['iregexp'])) {
			$regexp=$params['iregexp'];
			$inverted=true;
		} else throw new \Exception("FieldVerification must have at least regexp nor iregexp argument");

		FieldForm::_addValidator(new Validator($params['name'], $regexp, $params['message'], $inverted));
	}

}

