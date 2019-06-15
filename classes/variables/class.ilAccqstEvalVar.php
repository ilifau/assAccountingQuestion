<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base eval variable
 * Evaluates a mathematical expression
 */
class ilAccqstEvalVar extends ilAccqstVariable
{
    /** @var string mathematical expression */
    private $expression;


    /**
     * Init the variable definition from an XML element
     * @param SimpleXMLElement $element
     * @throws ilException
     */
    public function initFromXmlElement(SimpleXMLElement $element)
    {
        if (empty((string) $element))
        {
            throw new ilException(sprintf($this->plugin->txt('missing_content'), $this->name));
        }

        $this->expression = trim((string) $element);
    }

    /**
     * Get the names of all variables that are directly used by this variable
     * @param string[] $names list of all available variable names
     * @return string[]
     */
    public function getUsedNames($names)
    {
        $used = [];
        foreach ($names as $name) {
            $pattern = '{' . $name . '}';

            if (strpos($this->expression, $pattern) !== false) {
                $used[$name] = true;
            }
        }
        return array_keys($used);
    }

}