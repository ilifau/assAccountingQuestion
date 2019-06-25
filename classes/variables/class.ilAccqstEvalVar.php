<?php
// Copyright (c) 2019 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv3, see LICENSE


/**
 * Base eval variable
 * Evaluates a mathematical expression
 */
class ilAccqstEvalVar extends ilAccqstVariable
{
    /** @var string mathematical expression */
    public $expression;


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
     * @return string[]
     */
    public function getUsedNames()
    {
        $used = [];
        foreach (array_keys($this->question->getVariables()) as $name) {
            $pattern = '{' . $name . '}';
            if (strpos($this->expression, $pattern) !== false) {
                $used[$name] = true;
            }
        }
        return array_keys($used);
    }


    /**
     * Calculate the value of the variable
     *
     * @param  integer  $depth calculation depth
     * @return bool     value is calculated
     * @see assFormulaQuestionResult::getReachedPoints()
     */
    public function calculateValue($depth = 0)
    {
        if (parent::calculateValue($depth)) {
            // variable is already calculated
            return true;
        }

        $this->expression = $this->question->substituteVariables($this->expression);

        $math = new EvalMath();
        $math->suppress_errors = true;
        $this->value = $math->evaluate($this->expression);

        return true;
    }

}