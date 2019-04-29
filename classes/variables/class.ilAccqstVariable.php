<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE

require_once __DIR__ . '/class.ilAccqstRangeVar.php';
require_once __DIR__ . '/class.ilAccqstSelectVar.php';

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

    /** @var string the currently selected or calculated value */
    public $value;


    /**
     * Get variables from an XML definition
     * @param   string
     * @param   ilassAccountingQuestionPlugin
     * @return self[]  (indexed by name)
     * @throws ilException
     */
    public static function getVariablesFromXmlCode($xml, $plugin)
    {
        $variables = [];

        $xml = @simplexml_load_string($xml);
        if (!($xml instanceof SimpleXMLElement && $xml->getName() == 'variables'))
        {
            throw new ilException($plugin->txt('missing_element_variables'));
        }

        foreach ($xml->children() as $element)
        {
            if ($element->getName() != 'var' || empty($element['name']))
            {
                throw new ilException($plugin->txt('missing_var_or_name'));
            }
            $name = (string) $element['name'];
            $type = (string) $element['type'];

            if (isset($variables[$name]))
            {
                // variable is already defined
                throw new ilException(sprintf($plugin->txt('double_variable_definition', $name)));
            }

            switch ($type)
            {
                case self::TYPE_RANGE:
                    $variable = ilAccqstRangeVar::getFromXmlElement($element, $plugin);
                    break;
                case self::TYPE_SELECT:
                    $variable = ilAccqstSelectVar::getFromXmlElement($element, $plugin);
                    break;
                default:
                    // unknown type
                    throw new ilException($plugin->txt(sprintf('unknown_variable_type', $name)));
            }

            $variables[$name] = $variable;
        }

        return $variables;
    }


    /**
     * Get a variable definition from an XML element
     * (null in case of parse error)
     * @param SimpleXMLElement $element
     * @param ilassAccountingQuestionPlugin $plugin
     * @return self
     * @throws ilException
     */
    public static function getFromXmlElement(SimpleXMLElement $element, ilassAccountingQuestionPlugin $plugin)
    {
        return null;
    }


    /**
     * Get the floating point value of a string
     * @param  string $a_string
     * @return float
     */
    public static function stringToFloat($a_string)
    {
        if ($a_string)
        {
            $string = str_replace('.', '', $a_string);
            $string = str_replace(' ', '', $string);
            $string = str_replace(',', '.', $string);
            return floatval($string);
        }
        else
        {
            return 0;
        }
    }

    /**
     * Calculate the values of all variables
     * @param self[] $variables
     * @return bool
     */
    public static function calculateValues(&$variables)
    {
        foreach ($variables as $var)
        {
            if (!$var->calculateValue($variables))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * ilAccqstVariable constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }


    /**
     * Calculate the value of the variable
     * @param self[]
     * @param  integer  calculation depth
     * @return bool
     */
    public function calculateValue(&$variables, $depth = 0)
    {
        if ($depth > self::MAX_DEPTH)
        {
            return false;
        }
    }
}