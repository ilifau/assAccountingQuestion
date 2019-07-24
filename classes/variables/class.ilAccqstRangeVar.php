<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base range variable
 * Selects a value from a range of values
 */
class ilAccqstRangeVar extends ilAccqstVariable
{
    /** @var float minimum value */
    public $min;

    /** @var float maximum value */
    public $max;

    /** @var float step for creating values between min and max */
    public $step = 1;


    /**
     * Init the variable definition from an XML element
     * @param SimpleXMLElement $element
     * @throws ilException
     */
    public function initFromXmlElement(SimpleXMLElement $element)
    {
        if (empty($element['min']) || empty($element['max']))
        {
            throw new ilException(sprintf($this->plugin->txt('missing_min_max'), $this->name));
        }

        $this->min = $this->plugin->toFloat($element['min']);
        $this->max = $this->plugin->toFloat($element['max']);
        if (!empty($element['step']))
        {
            $this->step = $this->plugin->toFloat($element['step']);
        }
    }

    /**
     * Get the names of all variables that are directly used by this variable
     * @return string[]
     */
    public function getUsedNames()
    {
        return [];
    }

    /**
     * Calculate the value of the variable
     *
     * @param  integer  $depth calculation depth
     * @return bool     value is calculated
     */
    public function calculateValue($depth = 0)
    {
        if (parent::calculateValue($depth)) {
            // variable is already calculated
            return true;
        }

       $maxsteps = (int) (($this->max - $this->min) / $this->step);
       $num = rand(0, $maxsteps);

       $this->value = (float) ( $this->min + $num * $this->step);

       return true;
    }
}