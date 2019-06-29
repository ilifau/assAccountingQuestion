<?php
/**
 * Copyright (c) 2013 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

include_once "./Modules/Test/classes/inc.AssessmentConstants.php";

/**
 * Class for accounting questions
 *
 * @author    Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 * @version    $Id:  $
 * @ingroup ModulesTestQuestionPool
 */
class assAccountingQuestion extends assQuestion
{
	/**
	 * Reference of the plugin object
	 * @var object
	 */
	private $plugin;

	/**
	 * List of part objects
	 * @var assAccountingQuestionPart[]
	 */
	private $parts = array();

	/**
	 * XML representation of accounts definitions
	 * (stored in the DB)
	 * @var string
	 */
	private $accounts_xml = '';

	/**
	 * Array representation of accounts definitions
	 * Is set by setAccountsXML()
	 * @var array
	 */
	private $accounts_data = [];

	/**
	 * Display mode of accounts in the select fields
	 * Is set by setAccountsXML()
	 * @var string
	 */
	private $accounts_display = 'both'; // 'number', 'title', 'both'


    /**
     * XML representation of variables definitions
     * (stored in the DB)
     * @var string
     */
    private $variables_xml = '';

    /**
	 * Random variables of the question
	 * Is set implictly by setVariablesXML()
     * @var ilAccqstVariable[]
     */
	private $variables = array();

    /**
	 * Error from analyze functions
     * @var string
     */
	private $analyze_error = '';

    /**
     * Precision for comparing floating point values
     * @var int
     */
	private $precision = 10;

	/**
	 * ilAccountingQuestion constructor
	 *
	 * The constructor takes possible arguments an creates an instance of the ilAccountingQuestion object.
	 *
	 * @param string $title A title string to describe the question
	 * @param string $comment A comment string to describe the question
	 * @param string $author A string containing the name of the questions author
	 * @param integer $owner A numerical ID to identify the owner/creator
	 * @param string $question The question string of the single choice question
	 * @access public
	 * @see assQuestion:assQuestion()
	 */
	function __construct(
		$title = "",
		$comment = "",
		$author = "",
		$owner = -1,
		$question = ""
	)
	{
		parent::__construct($title, $comment, $author, $owner, $question);

		// init the plugin object
		$this->getPlugin();

		// include the needed classes
		$this->plugin->includeClass('class.assAccountingQuestionPart.php');
        $this->plugin->includeClass('variables/class.ilAccqstVariable.php');
	}

	/**
	 * @return ilassAccountingQuestionPlugin The plugin object
	 */
	public function getPlugin()
	{
		if ($this->plugin == null) {
			$this->plugin = ilPlugin::getPluginObject(IL_COMP_MODULE, "TestQuestionPool", "qst", "assAccountingQuestion");

		}
		return $this->plugin;
	}


	/**
	 * Returns true, if the question is complete for use
	 *
	 * @return boolean True, if the single choice question is complete for use, otherwise false
	 */
	public function isComplete()
	{
		if (($this->title) and ($this->author) and ($this->question) and ($this->points > 0)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get the analyzing error message
	 */
	public function getAnalyzeError()
	{
		return $this->analyze_error;
	}

	/**
	 * Saves a assFormulaQuestion object to a database
	 *
	 * @param    string        original id
	 * @param    boolean        save all parts, too
	 * @access    public
	 */
	function saveToDb($original_id = "", $a_save_parts = true)
	{
		global $DIC;

		$ilDB = $DIC->database();

		// collect the maximum points of all parts
		// must be done before basic data is saved
		$this->calculateMaximumPoints();


		// save the basic data (implemented in parent)
		// a new question is created if the id is -1
		// afterwards the new id is set
		$this->saveQuestionDataToDb($original_id);

		// save the account definition to a separate hash table
		$hash = hash("md5", $this->getAccountsXML());
		$ilDB->replace('il_qpl_qst_accqst_hash',
			array(
				'hash' => array('text', $hash)
			),
			array(
				'data' => array('clob', $this->getAccountsXML())
			)
		);

		// save data to DB
		$ilDB->replace('il_qpl_qst_accqst_data',
			array(
				'question_fi' => array('integer', $ilDB->quote($this->getId(), 'integer'))
			),
			array(
				'question_fi' => array('integer', $ilDB->quote($this->getId(), 'integer')),
				'account_hash' => array('text', $hash),
				'variables_def' => array('clob', $this->getVariablesXML()),
                'prec' => array('integer', $this->getPrecision())
			)
		);

		// save all parts (also a new one)
		if ($a_save_parts) {
			foreach ($this->parts as $part_obj) {
				$part_obj->write();
			}
		}
		// save stuff like suggested solutions
		// update the question time stamp and completion status
		parent::saveToDb();
	}

	/**
	 * Loads an assAccountingQuestion object from a database
	 *
	 * @param integer $question_id A unique key which defines the question in the database
	 */
	public function loadFromDb($question_id)
	{
		global $DIC;
		$ilDB = $DIC->database();

		// load the basic question data
		$result = $ilDB->query("SELECT qpl_questions.* FROM qpl_questions WHERE question_id = "
			. $ilDB->quote($question_id, 'integer'));

		$data = $ilDB->fetchAssoc($result);
		$this->setId($question_id);
		$this->setTitle($data["title"]);
		$this->setComment($data["description"]);
		$this->setSuggestedSolution($data["solution_hint"]);
		$this->setOriginalId($data["original_id"]);
		$this->setObjId($data["obj_fi"]);
		$this->setAuthor($data["author"]);
		$this->setOwner($data["owner"]);
		$this->setPoints($data["points"]);

		$this->setQuestion(ilRTE::_replaceMediaObjectImageSrc($data["question_text"], 1));
		$this->setEstimatedWorkingTime(substr($data["working_time"], 0, 2), substr($data["working_time"], 3, 2), substr($data["working_time"], 6, 2));

		try {
			$this->setAdditionalContentEditingMode($data['add_cont_edit_mode']);
		} catch (ilTestQuestionPoolException $e) {
		}

		// get the question data
        $result = $ilDB->query(
            "SELECT account_hash, variables_def, prec FROM il_qpl_qst_accqst_data "
            . " WHERE question_fi =" . $ilDB->quote($question_id, 'integer'));
        $data = $ilDB->fetchAssoc($result);

        $hash = $data['account_hash'];
        $this->setVariablesXML($data['variables_def']);
        $this->setPrecision($data['prec']);

        // get the hash value for accounts definition
		$result = $ilDB->query(
			"SELECT data FROM il_qpl_qst_accqst_hash "
			. " WHERE hash =" . $ilDB->quote($hash, 'text'));

		$data = $ilDB->fetchAssoc($result);
		$this->setAccountsXML($data["data"]);

		// load the question parts
		$this->loadParts();

		// loads additional stuff like suggested solutions
		parent::loadFromDb($question_id);
	}


	/**
	 * Load the question parts
	 */
	function loadParts()
	{
		$this->parts = assAccountingQuestionPart::_getOrderedParts($this);
	}

    /**
     * Duplicates an assAccountingQuestion
     *
     * @access public
     * @param bool $for_test
     * @param string $title
     * @param string $author
     * @param string $owner
     * @param int $testObjId
     * @return int
     */
	function duplicate($for_test = true, $title = "", $author = "", $owner = "", $testObjId = null)
	{
		if ($this->getId() <= 0) {
			// The question has not been saved. It cannot be duplicated
			return 0;
		}

		// make a real clone to keep the object unchanged
		// therefore no local variables are needed for the original ids 
		// the parts, however, still point to the original ones
		$clone = clone $this;

		$original_id = assQuestion::_getOriginalId($this->getId());
		$clone->setId(-1);

		if ((int)$testObjId > 0) {
			$clone->setObjId($testObjId);
		}

		if ($title) {
			$clone->setTitle($title);
		}
		if ($author) {
			$clone->setAuthor($author);
		}
		if ($owner) {
			$clone->setOwner($owner);
		}

		if ($for_test) {
			$clone->saveToDb($original_id, false);
		} else {
			$clone->saveToDb('', false);
		}

		// clone all parts
		// must be done after saving when new id is set
		$clone->cloneParts($this);

		// copy question page content
		$clone->copyPageOfQuestion($this->getId());
		// copy XHTML media objects
		$clone->copyXHTMLMediaObjectsOfQuestion($this->getId());

		// call the event handler for duplication
		$clone->onDuplicate($this->getObjId(), $this->getId(), $clone->getObjId(), $clone->getId());

		return $clone->getId();
	}

    /**
     * Copies an assAccountingQuestion object
     *
     * @access public
     * @param int $target_questionpool_id
     * @param string $title
     * @return int
     */
	function copyObject($target_questionpool_id, $title = "")
	{
		if ($this->getId() <= 0) {
			// The question has not been saved. It cannot be duplicated
			return 0;
		}

		// make a real clone to keep the object unchanged
		// therefore no local variables are needed for the original ids
		// but parts will still point to the original ones
		$clone = clone $this;

		$original_id = assQuestion::_getOriginalId($this->getId());
		$source_questionpool_id = $this->getObjId();
		$clone->setId(-1);
		$clone->setObjId($target_questionpool_id);
		if ($title) {
			$clone->setTitle($title);
		}

		// save the clone data
		$clone->saveToDb('', false);

		// clone all parts
		// must be done after saving when new id is set
		$clone->cloneParts($this);

		// copy question page content
		$clone->copyPageOfQuestion($original_id);
		// copy XHTML media objects
		$clone->copyXHTMLMediaObjectsOfQuestion($original_id);

		// call the event handler for copy
		$clone->onCopy($source_questionpool_id, $original_id, $clone->getObjId(), $clone->getId());

		return $clone->getId();
	}

	/**
	 * Synchronize a question with its original
	 *
	 * @access public
	 */
	function syncWithOriginal()
	{
		if( !$this->getOriginalId() )
		{
			return;
		}

		// get the original pool
		$originalObjId = self::lookupParentObjId($this->getOriginalId());
		if (!$originalObjId)
		{
			return;
		}

		$id = $this->getId();
		$objId = $this->getObjId();
		$original = $this->getOriginalId();

		$this->beforeSyncWithOriginal($original, $id, $originalObjId, $objId);

		// get the original question as clone of the current
		// this keeps all current properties
		$orig = clone $this;

		// change the ids to their originals
		$orig->setId($this->getOriginalId());
		$orig->setOriginalId(NULL);
		$orig->setObjId($originalObjId);
		$orig->saveToDb('', false);

		// delete all original parts and set clones of own parts
		// first load parts because they still point to the own parts
		$orig->loadParts();
		$orig->deleteParts();
		$orig->cloneParts($this);

		// copy the question page
		$orig->deletePageOfQuestion($orig->getId());
		$orig->createPageObject();
		$orig->copyPageOfQuestion($this->getId());

		// now we are back at the current question
		$this->updateSuggestedSolutions($orig->getId());
		$this->syncXHTMLMediaObjectsOfQuestion();

		$this->afterSyncWithOriginal($original, $id, $originalObjId, $objId);
		$this->syncHints();
	}

	/**
	 * Clone the parts of another question
	 *
	 * @param    assAccountingQuestion    $a_source_obj
	 * @access    public
	 */
	private function cloneParts($a_source_obj)
	{
		$cloned_parts = array();

		foreach ($a_source_obj->getParts() as $part_obj) {
			// cloning is handled in the part object
			// at this time the parent points to the original question
			$part_clone = clone $part_obj;

			// reset the part_id so that a new part is written to the database
			$part_clone->setPartId(0);

			// now set the new parent
			// which also sets the question id
			$part_clone->setParent($this);

			// write the new part object to db
			$part_clone->write();

			$cloned_parts[] = $part_clone;
		}

		$this->parts = $cloned_parts;
	}


	/**
	 * Delete all parts of a question
	 */
	function deleteParts()
	{
		foreach ($this->parts as $part_obj) {
			$part_obj->delete();
		}
		$this->parts = array();
	}

	/**
	 * get the parts of the question
	 * @return assAccountingQuestionPart[]
	 */
	function getParts()
	{
		return $this->parts;
	}


	/*
	 * get a part by its id
	 * 
	 * if part is not found, an new part will be delivered
	 */
	function getPart($a_part_id = 0)
	{
		foreach ($this->parts as $part_obj) {
			if ($part_obj->getPartId() == $a_part_id) {
				return $part_obj;
			}
		}

		// add and return a new part object
		$part_obj = new assAccountingQuestionPart($this);
		$this->parts[] = $part_obj;
		return $part_obj;
	}

    /**
     * remove a part from the list of parts
     * @param int $a_part_id
     * @return bool
     */
	function deletePart($a_part_id)
	{
		foreach ($this->parts as $part_obj) {
			if ($part_obj->getPartId() == $a_part_id) {
				// delete the found part
				if ($part_obj->delete()) {
					unset($this->parts[$a_part_id]);
					$this->calculateMaximumPoints();
					$this->saveToDB('', false);
					return true;
				}
			}
		}

		// part not found
		return false;
	}


	/**
	 * Analyze the XML accounts definition
	 *
	 * Data is set in class variable 'accounts_data' (not stored in db)
	 *
	 * @param    string        xml definition of the accounts
	 * @return    boolean        definition is ok (true/false)
	 */
	public function setAccountsXML($a_accounts_xml)
	{
	    // default values
        $this->accounts_data = array();
        $this->accounts_display = "beide";

        $xml = @simplexml_load_string($a_accounts_xml);

		if (!is_object($xml)) {
			return false;
		}

		$type = $xml->getName();
		if ($type != 'konten') {
			return false;
		}

		$display = (string) $xml['anzeige'];

		// init accounts data (not yed saved in db)
		$data[] = array();

		foreach ($xml->children() as $child) {
			// each account is an array of properties
			$account = array();

			$account['title'] = (string)$child['titel'];
			$account['number'] = (string)$child['nummer'];

			switch (strtolower($display)) {
				case 'nummer':
					$account['text'] = $account['number'];
					break;

				case 'titel':
					$account['text'] = $account['title'];
					break;

				default:
					$account['text'] = $account['number'] . ': ' . $account['title'];
					break;
			}

			// add the account to the data
			$data[] = $account;
		}

		// set data if ok
        $this->accounts_xml = $a_accounts_xml;
        $this->accounts_data = $data;
        $this->accounts_display = $display;
		return true;
	}


	/**
	 * get the accounts data
	 *
	 * @return    array    accounts data
	 */
	public function getAccountsData()
	{
		return (array) $this->accounts_data;
	}


	/**
	 * get the account according to an input text
	 *
	 * @param    string    input text
	 * @return    array    account data ('number', 'title', 'text')
	 */
	public function getAccount($a_text)
	{
		foreach ($this->getAccountsData() as $account) {
			if ((int)$account['number'] == (int)$a_text
				or strtolower($account['title']) == strtolower($a_text)
				or strtolower($account['text']) == strtolower($a_text)
			) {
				return $account;
			}
		}
		return array();
	}

	/**
	 * get the account text from an account number
	 * @param string	Account number
	 * @return string	Account text
	 */
	public function getAccountText($number)
	{
		foreach ($this->getAccountsData() as $account) {
			if ($account['number'] == $number) {
				return $account['text'];
			}
		}
		return "";
	}


	/**
	 * get the accounts definition as XML
	 *
	 * @return    string    xml definition of the accounts
	 */
	public function getAccountsXML()
	{
		return $this->accounts_xml;
	}


    /**
	 * set the variables definitions from XML
     * @param string $a_variables_xml	code
	 * @return bool					definition is ok
     */
	public function setVariablesXML($a_variables_xml)
	{
		try
		{
            $variables = ilAccqstVariable::getVariablesFromXmlCode($a_variables_xml, $this);
		}
		catch (Exception $e)
		{
			$this->analyze_error = $e->getMessage();
			return false;
		}


        $this->variables_xml = $a_variables_xml;
        $this->variables = $variables;
		return true;
	}


    /**
     * get the variables definition as XML
     *
     * @return    string    xml definition of the variables
     */
    public function getVariablesXML()
    {
        return $this->variables_xml;
    }

    /**
	 * Get the list of variables
     * @return ilAccqstVariable[]
     */
	public function getVariables()
	{
		return (array) $this->variables;
	}

    /**
     * Get the list of variables
     * @return array
     */
    public function getVariablesDump()
    {
        $dump = [];
        foreach ($this->variables as $var) {
            $dump[$var->name] = get_object_vars($var);
        }

        return $dump;
    }

    /**
     * Calculate the values of all variables
     * A calculation error mesage is provided with getAnalyzeError()
     * @return bool all variables are calculated
     */
    public function calculateVariables()
    {
        try {
            foreach ($this->variables as $name => $var)
            {
                if (!$var->calculateValue()) {
                    $this->analyze_error = sprintf($this->plugin->txt('var_not_calculated'), $var->name);
                    return false;
                }
            }
        }
        catch (Exception $e) {
            $this->analyze_error = $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Set the values of the variables from a user solution
     * Otherwise calculate them
     * @param array $userSolution value1 => value2
     * @return bool the variables are complete in the user solution
     */
    public function initVariablesFromUserSolution($userSolution = [])
    {
        foreach ($userSolution as $value1 => $value2) {
            if ($value1 == 'accqst_vars') {
                $values = unserialize($value2);
                foreach ($values as $name => $value) {
                    if (isset($this->variables[$name])) {
                        $this->variables[$name] = $value;
                    }
                }
            }
        }

        $complete = true;
        foreach ($this->variables as $variable) {
            if (!isset($variable->value)) {
                $complete = false;
            }
        }

        if (!$complete) {
            // be sure that variables have values if user solution is empty
            $this->calculateVariables();

        }

        return $complete;
    }

    /**
     * Add variables to a user solution
     * @param array $userSolution
     * @return array value1 => value2
     */
    public function addVariablesToUserSolution($userSolution = [])
    {
        $values = [];
        foreach ($this->variables as $name => $var) {
            $values['name'] = $var->getString();
        }
        $userSolution['accqst_var'] = serialize($values);

        return $userSolution;
    }


    /**
     * Substitute the referenced variables in a string
     * @param string $string
     * @param bool $numeric  use . as decimal point
     * @return $string
     */
    public function substituteVariables($string, $numeric = false) {
        foreach ($this->getVariables() as $name => $var) {
            $pattern = '{' . $name . '}';
            if (strpos($string, $pattern) !== false) {
                if ($numeric) {
                    $value = (string) $var->getFloat();
                }
                else {
                    $value = $var->getString();
                }

                $string = str_replace($pattern, $value, $string);
            }
        }
        return $string;
    }

    /**
     * Get the calculation precision
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }


    /**
     * Set the calculation precision
     * @param int $precision
     */
    public function setPrecision($precision)
    {
        $this->precision = (int) $precision;
    }

    /**
     * Check if two values are equal
     * @param float $val1;
     * @param float $val2;
     * @return bool;
     */
    public function equals($val1, $val2)
    {
        return (abs($val1 - $val2) < (0.1 ** $this->getPrecision()));
    }


    /**
	 * Calculate the maximum points
	 *
	 * This should be done whenever a part or booking file is changed
	 */
	public function calculateMaximumPoints()
	{
		$points = 0;
		foreach ($this->parts as $part_obj) {
			$points += $part_obj->getMaxPoints();
		}

		$this->setPoints($points);
	}


	/**
	 * Get a submitted solution array from $_POST
	 *
	 * The return value is used by:
	 *        savePreviewData()
	 *        saveWorkingData()
	 *        calculateReachedPointsForSolution()
	 *
	 * @return    array    value1 => value2
	 */
	protected function getSolutionSubmit()
	{
		$inputs = [];

		foreach ($this->getParts() as $part_obj)
		{
			$part_id = $part_obj->getPartId();

			// part_id is needed, because inputs are concatenated for storage
			// @see self::getSolutionStored()
			$xml = '<input part_id="'.$part_id.'">';
			for ($row = 0; $row < (int)$part_obj->getMaxLines(); $row++)
			{
				$prefix = 'q_' . $this->getId() . '_part_' .$part_id . '_row_' . $row .'_';

				$xml .= '<row ';
				$xml .= 'rightValueMoney="' . (string) $_POST[$prefix.'amount_right'] . '" ';
				$xml .= 'leftValueMoney="' . (string) $_POST[$prefix.'amount_left'] . '" ';
				$xml .= 'rightValueRaw="' . (string) $_POST[$prefix.'amount_right'] . '" ';
				$xml .= 'leftValueRaw="' . (string) $_POST[$prefix.'amount_left'] . '" ';
				$xml .= 'rightAccountNum="' . (string) $_POST[$prefix.'account_right'] . '" ';
				$xml .= 'leftAccountNum="' . (string) $_POST[$prefix.'account_left'] . '" ';
				$xml .= 'rightAccountRaw="' . $this->getAccountText((string) $_POST[$prefix.'account_right']) . '" ';
				$xml .= 'leftAccountRaw="' . $this->getAccountText((string) $_POST[$prefix.'account_left']) . '"/> ';
			}
			$xml .= '</input>';

            $inputs[] = $xml;
		}

        $value1 = 'accqst_input';						    // key to idenify the storage format
        $value2 = implode('<partBreak />', $inputs);	// concatenated xml inputs for all parts

        return [$value1 => $value2];
	}


	/**
	 * Get a solution array from the database
	 *
	 * The return value is used by:
	 *        savePreviewData()
	 *        saveWorkingData()
	 *        calculateReachedPointsForSolution()
	 *
	 * @param	integer		active id of the user
	 * @param	integer		test pass
	 * @param	mixed		true: get authorized solution, false: get intermediate solution, null: prefer intermediate
	 * @return  array    	value1 => value2
	 */
	public function getSolutionStored($active_id, $pass, $authorized = null)
	{
		if (is_null($authorized))
		{
			// assAccountingQuestionGUI::getTestOutput() takes the latest storage
			$rows = $this->getUserSolutionPreferingIntermediate($active_id, $pass);
		}
		else
		{
			// other calls should explictly indicate whether to use the authorized or intermediate solutions
			$rows = $this->getSolutionValues($active_id, $pass, $authorized);
		}

		$userSolution = array();
		foreach ($rows as $row)
		{
			$userSolution[$row['value1']] = $row['value2'];
		}

		return $userSolution;
	}

	/**
	 * Get the XML parts of a user solution
	 * @param array $userSolution	value1 => value2
	 * @return array part_id =>  xml string
	 */
	public function getSolutionParts($userSolution)
	{
		$parts = array();

		foreach ($userSolution as $value1 => $value2)
		{
			if ($value1 == 'accqst_input') {
                // new format since 1.3.1
                // all inputs are in one row, concatenated by '<partBreak />'
                // @see self::saveWorkingData()
				$inputs = explode('<partBreak />', $value2);
				foreach ($inputs as $input)
				{
					$matches = array();
					if (preg_match('/part_id="([0-9]+)"/', $input, $matches))
					{
						$part_id = $matches[1];
                        $parts[$part_id] = $input;
					}
				}
			}
			else
			{
                // former format before 1.3.1, stored from the flash input
                // results are stored as key/value pairs
                // format of value1 is 'accqst_key_123' with 123 being the part_id
                //// key 'input' is the user input
				// keys 'student' and 'correct' are textual analyses, 'result' are the given points (not longer used)
				$split = explode('_', $value1);
			    $key = $split[1];
			    $part_id = $split[2];

				if ($key == 'input')
			    {
					$parts[$part_id] = $value2;
				}
			}
		}

		return $parts;
	}


    /**
     * Calculate the points a learner has reached answering the question in a test
     * The points are calculated from the given answers
     *
     * @param integer $active_id The Id of the active learner
     * @param integer $pass The Id of the test pass
	 * @param boolean $authorizedSolution (deprecated !!)
     * @param boolean $returndetails (deprecated !!)
     * @return integer/array $points/$details (array $details is deprecated !!)
     * @throws ilTestException
     */
	public function calculateReachedPoints($active_id, $pass = NULL,  $authorizedSolution = true, $returndetails = FALSE)
	{
		if ($returndetails)
		{
			throw new ilTestException('return details not implemented for ' . __METHOD__);
		}

		if (is_null($pass))
		{
			$pass = $this->getSolutionMaxPass($active_id);
		}

		$solution = $this->getSolutionStored($active_id, $pass, $authorizedSolution);

		return $this->calculateReachedPointsForSolution($solution);
	}

	/**
	 * Calculate the points a user has reached in a preview session
	 * @param ilAssQuestionPreviewSession $previewSession
	 * @return float
	 */
	public function calculateReachedPointsFromPreviewSession(ilAssQuestionPreviewSession $previewSession)
	{
		return $this->calculateReachedPointsForSolution($previewSession->getParticipantsSolution());
	}


	/**
	 * Calculate the reached points from a solution array
	 *
	 * @param   array    value1 => value2
	 * @return  float    reached points
	 */
	protected function calculateReachedPointsForSolution($solution)
	{
	    $solutionParts = $this->getSolutionParts($solution);
		$points = 0;
		foreach ($this->getParts() as $part_obj)
		{
			$part_id = $part_obj->getPartId();
			$part_obj->setWorkingXML($solutionParts[$part_id]);
			$points += $part_obj->calculateReachedPoints();
		}

		// return the raw points given to the answer
		// these points will afterwards be adjusted by the scoring options of a test
		return $points;
	}

	/**
	 * Save the submitted input in a preview session
	 * @param ilAssQuestionPreviewSession $previewSession
	 */
	protected function savePreviewData(ilAssQuestionPreviewSession $previewSession)
	{
        $this->initVariablesFromUserSolution($previewSession->getParticipantsSolution());
        $userSolution = $this->addVariablesToUserSolution($this->getSolutionSubmit());

		$previewSession->setParticipantsSolution($userSolution);
	}


	/**
	 * Saves the learners input of the question to the database
	 *
	 * @param    integer	$active_id
	 * @param	 integer	$pass
	 * * @param	 boolean	$authorized
	 * @return   boolean 	successful saving
	 *
	 * @see    self::getSolutionStored()
	 */
	function saveWorkingData($active_id, $pass = NULL,  $authorized = true)
	{
		if (is_null($pass))
		{
			$pass = ilObjTest::_getPass($active_id);
		}

		// get the values to be stored
		$userSolution = $this->getSolutionSubmit();

		// update the solution with process lock
        $this->getProcessLocker()->executeUserSolutionUpdateLockOperation(function() use ($active_id, $pass, $authorized, $userSolution) {
            $this->removeCurrentSolution($active_id, $pass, $authorized);
            foreach ($userSolution as $value1 => $value2) {
                $this->saveCurrentSolution($active_id, $pass, $value1, $value2, $authorized);
            }
        });

		// log the saving, we assume that values have been entered
		if (ilObjAssessmentFolder::_enabledAssessmentLogging())
		{
			$this->logAction($this->lng->txtlng("assessment", "log_user_entered_values", ilObjAssessmentFolder::_getLogLanguage()), $active_id, $this->getId());
		}

		return true;
	}



	/**
	 * Reworks the already saved working data if neccessary
	 *
	 * @abstract
	 * @access protected
	 * @param integer $active_id
	 * @param integer $pass
	 * @param boolean $obligationsAnswered
	 * * @param boolean $authorized
	 */
	protected function reworkWorkingData($active_id, $pass, $obligationsAnswered, $authorized)
	{
		// nothing to rework!
	}

    /**
     * @param int $active_id
     * @param int $pass
     * @param bool|true $authorized
     * @global ilDBInterface $ilDB
     *
     * @return int
     */
    public function removeCurrentSolution($active_id, $pass, $authorized = true)
    {
        global $ilDB;

        if($this->getStep() !== NULL)
        {
            $query = "
				DELETE FROM tst_solutions
				WHERE active_fi = %s
				AND question_fi = %s
				AND pass = %s
				AND step = %s
				AND authorized = %s
				AND value1 <> 'accqst_vars'
			";

            return $ilDB->manipulateF($query, array('integer', 'integer', 'integer', 'integer', 'integer'),
                array($active_id, $this->getId(), $pass, $this->getStep(), (int)$authorized)
            );
        }
        else
        {
            $query = "
				DELETE FROM tst_solutions
				WHERE active_fi = %s
				AND question_fi = %s
				AND pass = %s
				AND authorized = %s
				AND value1 <> 'accqst_vars'
			";

            return $ilDB->manipulateF($query, array('integer', 'integer', 'integer', 'integer'),
                array($active_id, $this->getId(), $pass, (int)$authorized)
            );
        }
    }

    public function removeExistingSolutions($activeId, $pass)
    {
        global $ilDB;

        $query = "
			DELETE FROM tst_solutions
			WHERE active_fi = %s
			AND question_fi = %s
			AND pass = %s
			AND value1 <> 'accqst_vars'
		";

        if( $this->getStep() !== NULL )
        {
            $query .= " AND step = " . $ilDB->quote((int)$this->getStep(), 'integer') . " ";
        }

        return $ilDB->manipulateF($query, array('integer', 'integer', 'integer'),
            array($activeId, $this->getId(), $pass)
        );
    }


    /**
     * Lookup if an authorized or intermediate solution exists
     * @param 	int 		$activeId
     * @param 	int 		$pass
     * @return 	array		['authorized' => bool, 'intermediate' => bool]
     */
    public function lookupForExistingSolutions($activeId, $pass)
    {
        /** @var $ilDB \ilDBInterface  */
        global $ilDB;

        $return = array(
            'authorized' => false,
            'intermediate' => false
        );

        $query = "
			SELECT authorized, COUNT(*) cnt
			FROM tst_solutions
			WHERE active_fi = %s
			AND question_fi = %s
			AND pass = %s
			AND value1 <> 'accqst_vars'
		";

        if( $this->getStep() !== NULL )
        {
            $query .= " AND step = " . $ilDB->quote((int)$this->getStep(), 'integer') . " ";
        }

        $query .= "
			GROUP BY authorized
		";

        $result = $ilDB->queryF($query, array('integer', 'integer', 'integer'), array($activeId, $this->getId(), $pass));

        while ($row = $ilDB->fetchAssoc($result))
        {
            if ($row['authorized']) {
                $return['authorized'] = $row['cnt'] > 0;
            }
            else
            {
                $return['intermediate'] = $row['cnt'] > 0;
            }
        }
        return $return;
    }


    /**
	 * Returns the question type of the question
	 *
	 * @return string The question type of the question
	 */
	public function getQuestionType()
	{
		return "assAccountingQuestion";
	}

	/**
	 * Returns the names of the additional question data tables
	 *
	 * all tables must have a 'question_fi' column
	 * data from these tables will be deleted if a question is deleted
	 *
	 * TODO: the hash table for accounts definitions needs a separate cleanup
	 *
	 * @return array    the names of the additional tables
	 */
	public function getAdditionalTableName()
	{
		return array('il_qpl_qst_accqst_data',
			'il_qpl_qst_accqst_part');
	}


	/**
	 * Collects all text in the question which could contain media objects
	 * which were created with the Rich Text Editor
	 */
	function getRTETextWithMediaObjects()
	{
		$text = parent::getRTETextWithMediaObjects();
		foreach ($this->getParts() as $part_obj) {
			$text .= $part_obj->getText();
		}
		return $text;
	}

    /**
     * {@inheritdoc}
     */
	public function setExportDetailsXLS($worksheet, $startrow, $active_id, $pass)
	{
        $worksheet->setFormattedExcelTitle($worksheet->getColumnCoord(0) . $startrow, $this->plugin->txt($this->getQuestionType()));
        $worksheet->setFormattedExcelTitle($worksheet->getColumnCoord(1) . $startrow, $this->getTitle());

		$solution = $this->getSolutionStored($active_id, $pass, true);
		$solutionParts = $this->getSolutionParts($solution);

		$row = $startrow + 1;
		$part = 1;
		foreach ($this->getParts() as $part_obj)
		{
			$part_id = $part_obj->getPartId();

            $worksheet->setCell($row, 0, $this->getPlugin()->txt('accounting_table') . ' ' . $part);
            $worksheet->setBold($worksheet->getColumnCoord(0) . $row);

			// the excel fields can be filled from the stored input
			$part_obj->setWorkingXML($solutionParts[$part_id]);
			$part_obj->calculateReachedPoints();
			$data = $part_obj->getWorkingData();

			$point = $this->plugin->txt('point');
			$points = $this->plugin->txt('points');

            $worksheet->setCell($row, 1, $data['headerLeft']);
            $worksheet->setCell($row, 2, $data['headerRight']);
			$row++;

			foreach($data['record']['rows'] as $r)
			{
				$left =  $r['leftAccountText'] . ' ' . $r['leftValueRaw']. ' ('. $r['leftPoints']. ' '. ($r['leftPoints'] == 1 ? $point : $points) . ')';
				$right =  $r['rightAccountText'] . ' ' . $r['rightValueRaw']. ' ('. $r['rightPoints']. ' '. ($r['rightPoints'] == 1 ? $point : $points) . ')';

                $worksheet->setCell($row, 1, $left);
                $worksheet->setCell($row, 2, $right);
				$row++;
			}

			foreach (array('bonusOrderLeft','bonusOrderRight','malusCountLeft','malusCountRight','malusSumsDiffer') as $key)
			{
				if($data['record'][$key] != 0)
				{
                    $worksheet->setCell($row, 1, $this->plugin->txt($key));
                    $worksheet->setCell($row, 2, $data['record'][$key] .' ' . (abs($data['record'][$key]) == 1 ? $point : $points));
					$row++;
				}
			}

			$part++;
		}
		return $row + 1;
	}

	/**
	 * Creates a question from a QTI file
	 *
	 * Receives parameters from a QTI parser and creates a valid ILIAS question object
	 *
	 * @param object $item The QTI item object
	 * @param integer $questionpool_id The id of the parent questionpool
	 * @param integer $tst_id The id of the parent test if the question is part of a test
	 * @param object $tst_object A reference to the parent test object
	 * @param integer $question_counter A reference to a question counter to count the questions of an imported question pool
	 * @param array $import_mapping An array containing references to included ILIAS objects
	 * @access public
	 */
	function fromXML(&$item, &$questionpool_id, &$tst_id, &$tst_object, &$question_counter, &$import_mapping)
	{
		$this->getPlugin()->includeClass("import/qti12/class.assAccountingQuestionImport.php");
		$import = new assAccountingQuestionImport($this);
		$import->fromXML($item, $questionpool_id, $tst_id, $tst_object, $question_counter, $import_mapping);
	}

	/**
	 * Returns a QTI xml representation of the question and sets the internal
	 * domxml variable with the DOM XML representation of the QTI xml representation
	 *
	 * @return string The QTI xml representation of the question
	 * @access public
	 */
	function toXML($a_include_header = true, $a_include_binary = true, $a_shuffle = false, $test_output = false, $force_image_references = false)
	{
		$this->getPlugin()->includeClass("export/qti12/class.assAccountingQuestionExport.php");
		$export = new assAccountingQuestionExport($this);
		return $export->toXML($a_include_header, $a_include_binary, $a_shuffle, $test_output, $force_image_references);
	}
}
