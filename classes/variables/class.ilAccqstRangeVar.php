<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base range variable
 * Selects a value from a range of values
 */
class ilAccqstRangeVar extends ilAccqstVariable
{
    /** @var string minimum value */
    public $min;

    /** @var string maximum value */
    public $max;

    /** @var string step for creating values between min and max */
    public $step;


    /**
     * Get a variable definition from an XML element
     * (null in case of parse error)
     * @param SimpleXMLElement $element
     * @return self|null
     */
    public static function getFromXmlElement(SimpleXMLElement $element)
    {
        $var = new self((string) $element['name']);

        if (empty($element['min']) || empty($element['max']))
        {
            return null;
        }

        $var->min = $element['min'];
        $var->max = $element['max'];
        if (!empty($element['step']))
        {
            $var->step = $element['step'];
        }

        return $var;
    }

}