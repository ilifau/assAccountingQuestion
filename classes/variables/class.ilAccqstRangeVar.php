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
    public $step = 1;


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
        $var = new self((string) $element['name']);

        if (empty($element['min']) || empty($element['max']))
        {
            throw new ilException(sprintf($plugin->txt('missing_min_max'), $var->name));
        }

        $var->min = (string) $element['min'];
        $var->max = (string) $element['max'];
        if (!empty($element['step']))
        {
            $var->step = (string) $element['step'];
        }

        return $var;
    }

}