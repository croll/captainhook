<?php
/**
* Smarty Compiled Resource Plugin
*
* @package Smarty
* @subpackage CompiledResources
* @author Uwe Tews
*/
/**
* Meta Data Container for Compiled Template Files
*
*
* @property string $content compiled content
*/
class Smarty_Compiled {

    /**
    * Compiled Filepath
    * @var string
    */
    public $filepath = null;

    /**
    * Compiled Timestamp
    * @var integer
    */
    public $timestamp = null;

    /**
    * Compiled Existance
    * @var boolean
    */
    public $exists = false;

    /**
    * Compiled Content Loaded
    * @var boolean
    */
    public $loaded = false;

    /**
    * Template was compiled
    * @var boolean
    */
    public $isCompiled = false;

    /**
    * Source Object
    * @var Smarty_Template_Source
    */
    public $source = null;

    /**
    * cache for Smarty_Compiled instances
    * @var array
    */
    public static $compileds = array();

    /**
    * Metadata properties
    *
    * populated by Smarty_Internal_Template::decodeProperties()
    * @var array
    */
    public $_properties = null;

    /**
    * create Compiled Object container
    *
    * @param Smarty__Internal_Template $_template template object this compiled object belongs to
    * @param Smarty__Internal_Template $_template template object
    */
    public function __construct(Smarty_Internal_Template $_template)
    {
        $this->source = $_template->source;
        $this->source->handler->populateCompiledFilepath($this, $_template);
        $this->timestamp = @filemtime($this->filepath);
        $this->exists = !!$this->timestamp;
    }

    /**
    * get rendered template output from compiled template
    *
    * @param Smarty__Internal_Template or Smarty $obj object of caller
    */
    public function getRenderedTemplate($obj, $_template) {
        if (!$this->source->uncompiled) {
            $_smarty_tpl = $_template;
            if ($this->source->recompiled) {
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::start_compile($_template);
                }
                $code = $_template->compiler->compileTemplate($_template);
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::end_compile($_template);
                }
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::start_render($_template);
                }
                try {
                    ob_start();
                    eval("?>" . $code);
                    unset($code);
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
            } else {
                if (!$this->exists || ($_template->smarty->force_compile && !$this->isCompiled)) {
                    $_template->compiler->compileTemplateSource($_template);
                    unset($_template->compiler);
                }
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::start_render($_template);
                }
                if (!$this->loaded) {
                    include($this->filepath);
                    if ($_template->mustCompile) {
                        // recompile and load again
                        $_template->compiler->compileTemplateSource($_template);
                        unset($_template->compiler);
                        include($this->filepath);
                    }
                    $this->loaded = true;
                } else {
                    $_template->decodeProperties($this->_properties, false);
                }
                try {
                    ob_start();
                    if (empty($_template->properties['unifunc']) || !is_callable($_template->properties['unifunc'])) {
                        throw new SmartyException("Invalid compiled template for '{$_template->template_resource}'");
                    }
                    $_template->properties['unifunc']($_template);
                    if (isset($_template->_capture_stack[0])) {
                        $_template->capture_error();
                    }
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
            }
        } else {
            if ($this->source->uncompiled) {
                if ($obj->smarty->debugging) {
                    Smarty_Internal_Debug::start_render($_template);
                }
                try {
                    ob_start();
                    $this->source->renderUncompiled($_template);
                } catch (Exception $e) {
                    ob_get_clean();
                    throw $e;
                }
            } else {
                throw new SmartyException("Resource '$this->source->type' must have 'renderUncompiled' method");
            }
        }
        $output = ob_get_clean();
        if (!$this->source->recompiled && empty($_template->properties['file_dependency'][$this->source->uid])) {
            $_template->properties['file_dependency'][$this->source->uid] = array($this->source->filepath, $this->source->timestamp, $this->source->type);
        }
        if ($_template->parent instanceof Smarty_Internal_Template) {
            $_template->parent->properties['file_dependency'] = array_merge($_template->parent->properties['file_dependency'], $_template->properties['file_dependency']);
            foreach ($_template->required_plugins as $code => $tmp1) {
                foreach ($tmp1 as $name => $tmp) {
                    foreach ($tmp as $type => $data) {
                        $_template->parent->required_plugins[$code][$name][$type] = $data;
                    }
                }
            }
        }
        if ($_template->caching != Smarty::CACHING_LIFETIME_SAVED && $_template->caching != Smarty::CACHING_LIFETIME_CURRENT) {
            // var_dump('renderTemplate', $_template->has_nocache_code, $_template->template_resource, $_template->properties['nocache_hash'], $_template->parent->properties['nocache_hash'], $_output);
            if (!empty($_template->properties['nocache_hash']) && !empty($_template->parent->properties['nocache_hash'])) {
                // replace nocache_hash
                $output = preg_replace("/{$_template->properties['nocache_hash']}/", $_template->parent->properties['nocache_hash'], $output);
                $_template->parent->has_nocache_code = $_template->parent->has_nocache_code || $_template->has_nocache_code;
            }
        }
        if ($obj->smarty->debugging) {
            Smarty_Internal_Debug::end_render($_template);
        }
        return  $output;
    }
}
?>