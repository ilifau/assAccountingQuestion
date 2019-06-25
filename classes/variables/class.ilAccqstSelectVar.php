<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base select variable
 * Selects a value from a list of values
 */
class ilAccqstSelectVar extends ilAccqstVariable
{
    /** @var string[] list of values */
    public $values = [];


    /**
     * Init the variable definition from an XML element
     * @param SimpleXMLElement $element
     * @throws ilException
     */
    public function initFromXmlElement(SimpleXMLElement $element)
    {
        foreach ($element->children() as $child)
        {
            if ($child->getName() != 'val')
            {
                throw new ilException(sprintf($this->plugin->txt('select_child_not_val'), $this->name));
            }

            $this->values[] = trim((string) $child);
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

        if (empty($this->values)) {
            return false;
        }

        $this->value = $this->values[array_rand($this->values)];

        return true;
    }

}