<?php

/**
 * Copyright (c) 2014 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg 
 * GPLv2, see LICENSE 
 */

/**
 * Cas Question object
 *
 * @author Fred Neumann <fred.neumann@ili.fau.de>
 * @author Jesus Copado <jesus.copado@ili.fau.de>
 * @version $Id$
 *
 */

class ilAssAccountingQuestionFeedback extends ilAssSingleOptionQuestionFeedback
{

    /**
     * object instance of current question
     * @var assAccountingQuestion
     */
    protected $questionOBJ = null;


    /**
     * returns the html of GENERIC feedback for the given question id for test presentation
     * (either for the complete solution or for the incomplete solution)
     *
     * @access public
     * @param integer $questionId
     * @param boolean $solutionCompleted
     * @return string $genericFeedbackTestPresentationHTML
     */
    public function getGenericFeedbackTestPresentation($questionId, $solutionCompleted)
    {
        $html = parent::getGenericFeedbackTestPresentation($questionId, $solutionCompleted);
        return $this->questionOBJ->substituteVariables($html, assAccountingQuestion::SUB_DISPLAY);
    }

}
