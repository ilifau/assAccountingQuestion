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
     * @return self|null
     */
    public static function getFromXmlElement(SimpleXMLElement $element)
    {
        $var = new self((string) $element['name']);

        foreach ($element->children() as $child)
        {
            if ($child->getName() != 'val')
            {
                return null;
            }

            $values[] = (string) $element;
        }

        return $var;
    }


}