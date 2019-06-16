<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once __DIR__ . '/class.ilAccqstEvalVar.php';
require_once __DIR__ . '/class.ilAccqstRangeVar.php';
require_once __DIR__ . '/class.ilAccqstSelectVar.php';
require_once __DIR__ . '/class.ilAccqstSwitchVar.php';


/**
 * Base class for variable definition
 */
abstract class ilAccqstVariable
{
    const MAX_DEPTH = 100;

    const TYPE_RANGE  = 'range';
    const TYPE_SELECT = 'select';
    const TYPE_SWITCH = 'switch';
    const TYPE_EVAL = 'eval';

    /** @var string name of the variable */
    public $name;

    /** @var mixed the currently selected or calculated value */
    public $value;

    /** @var ilAssAccountingQuestionPlugin */
    protected $plugin;

    /**
     * Get variables from an XML definition
     * @param   string $xml
     * @param   ilAssAccountingQuestionPlugin $plugin
     * @return self[]  (indexed by name)
     * @throws ilException
     */
    public static function getVariablesFromXmlCode($xml, $plugin)
    {
        $variables = [];

        $xml = @simplexml_load_string($xml);
        if (!($xml instanceof SimpleXMLElement && $xml->getName() == 'variables')) {
            throw new ilException($plugin->txt('missing_element_variables'));
        }

        foreach ($xml->children() as $element)
        {
            if ($element->getName() != 'var' || empty($element['name'])) {
                throw new ilException($plugin->txt('missing_var_or_name'));
            }
            $name = (string) $element['name'];
            $type = (string) $element['type'];

            if (isset($variables[$name])) {
                // variable is already defined
                throw new ilException(sprintf($plugin->txt('double_variable_definition'), $name));
            }

            switch ($type)
            {
                case self::TYPE_RANGE:
                    $variable = new ilAccqstRangeVar($name, $plugin);
                    break;
                case self::TYPE_SELECT:
                    $variable = new ilAccqstSelectVar($name, $plugin);
                    break;
                case self::TYPE_SWITCH:
                    $variable = new ilAccqstSwitchVar($name, $plugin);
                    break;
                case self::TYPE_EVAL:
                    $variable = new ilAccqstEvalVar($name, $plugin);
                    break;

                default:
                    // unknown type
                    throw new ilException($plugin->txt(sprintf('unknown_variable_type', $name)));
            }

            $variable->initFromXmlElement($element, $plugin);
            $variables[$name] = $variable;
        }

        return $variables;
    }


    /**
     * ilAccqstVariable constructor.
     * @param string $name
     * @param ilassAccountingQuestionPlugin $plugin
     */
    public function __construct($name, $plugin)
    {
        $this->name = $name;
        $this->plugin = $plugin;
    }

    /**
     * Init a variable definition from an XML element
     * (null in case of parse error)
     * @param SimpleXMLElement $element
     * @throws ilException
     */
    abstract public function initFromXmlElement(SimpleXMLElement $element);


    /**
     * Get the names of all variables that are directly used by this variable
     * @param string[] $names list of all available variable names
     * @return string[]
     */
    abstract public function getUsedNames($names);

    /**
     * Calculate the value of the variable
     * Child classes should override this and call the parent at the beginning
     *
     * @param self[] $variables
     * @param  integer  $depth calculation depth
     * @return bool     value is already calculated
     */
    public function calculateValue(&$variables, $depth = 0)
    {
        // probably a circular reference
        if ($depth > self::MAX_DEPTH) {
            throw new ilException($this->plugin->txt(sprintf('exceeded_calculation_depth', $this->name)));
        }

        // value is already calculated (e.g. by recursion from another variable)
        if (isset($this->value)) {
            return true;
        }

        // calculate all dependencies
        foreach ($this->getUsedNames(array_keys($variables)) as $name) {
            if ($name == $this->name) {
                throw new ilException($this->plugin->txt(sprintf('forbidden_self_reference', $this->name)));
            }
            if (!isset($variables[$name])) {
                throw new ilException($this->plugin->txt(sprintf('unknown_variable_reference', $this->name, $name)));
            }
            $variables[$name]->calculateValue($variables, $depth + 1);
        }

        // child class must calculate
        return false;
    }


    /**
     * Get the floating point value of the variable
     * @return float
     */
    public function getFloat()
    {
        if (is_float($this->value)) {
            return $this->value;
        }
        elseif (is_int($this->value)) {
            return (float) $this->value;
        }
        elseif (is_string($this->value)) {
            $string = $this->value;
            $string = str_replace('.', '', $string);
            $string = str_replace(' ', '', $string);
            $string = str_replace(',', '.', $string);
            return floatval($string);
        }
        else {
            return null;
        }
    }

    /**
     * Get the string value of the variable
     * @return string
     */
    public function getString()
    {
        if (is_string($this->value)) {
            return $this->value;
        }
        elseif (is_int($this->value) || is_float($this->value)) {
            return (string) $this->value;
        }
        else {
            return null;
        }
    }
}