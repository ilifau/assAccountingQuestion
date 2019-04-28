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
     * @return self[]|null  (indexed by name)
     */
    public static function getVariablesFromXmlCode($xml)
    {
        $variables = [];

        $xml = @simplexml_load_string($xml);
        if (!($xml instanceof SimpleXMLElement && $xml->getName() == 'variables'))
        {
            return null;
        }

        foreach ($xml->children() as $element)
        {
            if ($element->getName() != 'var' || empty($element['name']))
            {
                return null;
            }
            $name = (string) $element['name'];
            $type = (string) $element['type'];

            if (isset($variables[$name]))
            {
                // variable is already defined
                return null;
            }

            switch ($type)
            {
                case self::TYPE_RANGE:
                    $variables[$name] = ilAccqstRangeVar::getFromXmlElement($element);
                    break;
                case self::TYPE_SELECT:
                    $variables[$name] = ilAccqstSelectVar::getFromXmlElement($element);
                    break;
                default:
                    // unknown type
                    return null;
            }
        }

        return $variables;
    }


    /**
     * Get a variable definition from an XML element
     * (null in case of parse error)
     * @param SimpleXMLElement $element
     * @return self|null
     */
    public static function getFromXmlElement(SimpleXMLElement $element)
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