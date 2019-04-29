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

        foreach ($element->children() as $child)
        {
            if ($child->getName() != 'val')
            {
                throw new ilException(sprintf($plugin->txt('select_child_not_val'), $var->name));
            }

            $var->values[] = (string) $child;
        }

        return $var;
    }


}