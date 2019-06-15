<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base select variable
 * Selects a value from a list of values
 */
class ilAccqstSelectVar extends ilAccqstVariable
{
    /** @var string[] list of values */
    private $values = [];


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
     * @param string[] $names list of all available variable names
     * @return string[]
     */
    public function getUsedNames($names)
    {
        return [];
    }

}