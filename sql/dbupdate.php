<#1>
<?php
	/**
	 * Copyright (c) 2013 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
	 * GPLv2, see LICENSE
	 */

	/**
	 * Accounting Question plugin: database update script
	 *
	 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
	 * @version $Id$
	 */

	/*
	 * Create the new question type
	 */

	$res = $ilDB->queryF("SELECT * FROM qpl_qst_type WHERE type_tag = %s", array('text'), array('assAccountingQuestion')
	);

	if ($res->numRows() == 0)
	{
		$res = $ilDB->query("SELECT MAX(question_type_id) maxid FROM qpl_qst_type");
		$data = $ilDB->fetchAssoc($res);
		$max = $data["maxid"] + 1;

		$affectedRows = $ilDB->manipulateF("INSERT INTO qpl_qst_type (question_type_id, type_tag, plugin) VALUES (%s, %s, %s)", array("integer", "text", "integer"), array($max, 'assAccountingQuestion', 1)
		);
	}
?>
<#2>
<?php
	/*
	 * Add the additional table
	 *
	 */

	$fields = array(
		'question_fi' => array(
			'type' => 'integer',
			'length' => 4
		),

		'account_hash' => array(
		'type' => 'text',
		'length' => 255
		)
	);

	$ilDB->createTable("il_qpl_qst_accqst_data", $fields);
	$ilDB->addPrimaryKey("il_qpl_qst_accqst_data", array("question_fi"));
?>
<#3>
<?php
	/*
	 * Add the table for question parts
	 */

	$fields = array(
		'part_id' => array(
			'type' => 'integer',
			'length' => 4
		),
		'question_fi' => array(
			'type' => 'integer',
			'length' => 4
		),
		'position' => array(
			'type' => 'integer',
			'length' => 2
		),
		'text' => array(
			'type' => 'text',
			'length' => 4000
		),
		'max_points' => array(
			'type' => 'float'
		),
		'max_lines' => array(
			'type' => 'integer',
			'length' => 2
		),
		'booking_def' => array(
			'type' => 'clob'
		)
	);

	$ilDB->createTable("il_qpl_qst_accqst_part", $fields);
	$ilDB->createSequence("il_qpl_qst_accqst_part");

	$ilDB->addPrimaryKey("il_qpl_qst_accqst_part", array("part_id"));
	$ilDB->addIndex("il_qpl_qst_accqst_part", array("question_fi"), "qfi");
?>
<#4>
<?php
	/*
	 * Create hash table for accounts definitions
	 */
	if(!$ilDB->tableExists('il_qpl_qst_acc_hash')){
	$fields = array(
		'hash' => array(
			'type' => 'text',
			'length' => 255,
			'notnull' => true,
			'default' => 0
		),
		'data' => array(
			'type' => 'clob'
		)
	);
	$ilDB->createTable("il_qpl_qst_accqst_hash", $fields);
	$ilDB->addPrimaryKey("il_qpl_qst_accqst_hash", array("hash"));
	}
?>
<#5>
<?php
    /*
     * Create hash table for accounts definitions
     */
    if(!$ilDB->tableColumnExists('il_qpl_qst_accqst_data', 'variables_def'))
    {
        $ilDB->addTableColumn("il_qpl_qst_accqst_data", 'variables_def',
            array('type' => 'clob')
        );
    }
?>

