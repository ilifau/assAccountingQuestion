<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base switch variable
 * Selects a value based on another value
 */
class ilAccqstSwitchVar extends ilAccqstVariable
{
    private $check = '';

    /** @var array [[type => 'value'|'max'|'default', test => string, return => string], ... ] */
    private $cases = [];


    /**
     * Init the variable definition from an XML element
     * @param SimpleXMLElement $element
     * @throws ilException
     */
    public function initFromXmlElement(SimpleXMLElement $element)
    {
        if (empty($element['check'])) {
            throw new ilException(sprintf($this->plugin->txt('missing_check'), $this->name));
        }
        $this->check = (string) $element['check'];

        foreach ($element->children() as $child)
        {
            $case = [];
            switch ($child->getName()) {
                case 'case':
                    if (!empty($child['value'])) {
                        $case['type'] = 'value';
                        $case['test'] = (string) $child['value'];
                        $case['return'] = trim((string) $child);
                    }
                    elseif (!empty($child['max'])) {
                        $case['type'] = 'max';
                        $case['test'] = (string) $child['value'];
                        $case['return'] = trim((string) $child);
                    }
                    else {
                        throw new ilException(sprintf($this->plugin->txt('missing_case_value_or_max'), $this->name));
                    }
                    break;

                case 'default':
                    $case['type'] = 'default';
                    $case['test'] = '';
                    $case['return'] = trim((string) $child);
                    break;

                default:
                    throw new ilException(sprintf($this->plugin->txt('unknown_child'), $this->name));
            }
            $this->cases[] = $case;
        }
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

            if ($this->check == $pattern) {
                $used[$name] = true;
            }
            foreach ($this->cases as $case) {
                if ($case['test'] == $pattern || $case['return'] == $pattern) {
                    $used[$name] = true;
                }
            }
        }

        return array_keys($used);
    }
}