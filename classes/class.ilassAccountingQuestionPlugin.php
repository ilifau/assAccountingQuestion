<?php
/**
 * Copyright (c) 2013 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg 
 * GPLv2, see LICENSE 
 */

/**
* Accounting Question plugin
*
* @author Fred Neumann <frd.neumann@gmx.de>
* @version $Id$
*
*/
class ilassAccountingQuestionPlugin extends ilQuestionsPlugin
{
    final function getPluginName()
    {
        return "assAccountingQuestion";
    }

    final function getQuestionType()
    {
        return "assAccountingQuestion";
    }

    final function getQuestionTypeTranslation()
    {
        return $this->txt($this->getQuestionType());
    }

    /**
     * Define if debugging outputs should be shown
     * @return bool
     */
    public function isDebug()
    {
        return true;
    }


    /**
     * Get a value as floating point (decimals are separated by ,)
     * @param mixed $value
     * @return float
     */
    public function toFloat($value = null)
    {
        if (is_float($value)) {
            return $value;
        }
        elseif (is_int($value)) {
            return (float) $value;
        }
        elseif (is_string($value)) {
            $string = $value;
            $string = str_replace(' ', '', $string);
            $string = str_replace('.', '', $string);
            $string = str_replace(',', '.', $string);
            return floatval($string);
        }
        else {
            return 0;
        }
    }


    /**
     * Get a value as string (decimals are separated by ,)
     * @param mixed $value
     * @return string
     */
    public static function toString($value = null)
    {
        if (is_string($value)) {
            return $value;
        }
        elseif (is_int($value) || is_float($value)) {
            $string = strval($value);
            $string = str_replace(' ', '', $string);
            $string = str_replace(',', '', $string);
            $string = str_replace('.', ',', $string);
            return $string;
        }
        else {
            return '';
        }
    }
}