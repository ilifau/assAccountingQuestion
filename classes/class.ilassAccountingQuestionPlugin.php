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
}