<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base switch variable
 * Selects a value based on another value
 */
class ilAccqstSwitchVar extends ilAccqstVariable
{
    /** @var string  */
    public $check = '';

    /** @var array [[type => 'value'|'max'|'default', test => string, return => string], ... ] */
    public $cases = [];


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
                        $case['test'] = (string) $child['max'];
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
     * @return string[]
     */
    public function getUsedNames()
    {
        $used = [];
        foreach (array_keys($this->question->getVariables()) as $name) {
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

        $this->check = $this->plugin->toFloat($this->question->substituteVariables($this->check));

        foreach ($this->cases as $index => $case) {

            $this->cases[$index]['test'] = $this->plugin->toFloat($this->question->substituteVariables($case['test']));
            $this->cases[$index]['return'] = $this->question->substituteVariables($case['return']);
        }

        foreach ($this->cases as $case) {
            switch ($case['type']) {

                case 'value':
                    if ($this->question->equals($this->check, $case['test'])) {
                        $this->value = $case['return'];
                        return true;
                    }
                    break;


                case 'max':
                    if ($this->check <= $case['test']) {
                        $this->value = $case['return'];
                        return true;
                    }
                    break;

                case 'default':
                    $this->value = $case['return'];
                    return true;
            }
        }

        return false;
    }
}