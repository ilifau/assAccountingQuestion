<?php
/**
 * Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
 * Global Configuration for the Accounting Question
 */
class assAccountingQuestionConfig
{
    const DELIM_NONE = 'none';
    const DELIM_SPACE = 'space';
    const DELIM_DOT = 'dot';

    /** @var string type of delimiter used to separate thousands when showing user inputs and calculated numbers */
    public $thousands_delim_type;

    /**
     * @var bool allow a configuration of the thousands demlim per question
     */
    public $thousands_delim_per_question;

    /** @var ilTestArchiveCreatorPlugin $plugin */
    protected $plugin;

    /** @var ilSetting  */
    protected $settings;

    /**
     * Constructor
     * Initializes the configuration values
     *
     * @param ilassAccountingQuestionPlugin $plugin
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;

        $this->settings = new ilSetting('assAccountingQuestion');
        $this->thousands_delim_type = (string) $this->settings->get('thousands_delim_type', self::DELIM_DOT);
        $this->thousands_delim_per_question = (bool) $this->settings->get('thousands_delim_per_question', false);

    }


    /**
     * Save the configuration
     */
    public function save()
    {
        $this->settings->set('thousands_delim_type', $this->thousands_delim_type);
        $this->settings->set('thousands_delim_per_question', $this->thousands_delim_per_question ? '1' : '0');
    }

    /**
     * Get the effective the thousands delimiter
     */
    public function getThousandsDelim($type = null) {

        if (empty($type)) {
            $type = $this->thousands_delim_type;
        }
        switch ($type) {
            case self::DELIM_SPACE:
                return ' ';
            case self::DELIM_NONE:
                return '';
            case self::DELIM_DOT:
            default:
                return '.';
        }
    }

    /**
     * Get the effective the thousands delimiter
     */
    public function getThousandsDelimText($type = null) {

        if (empty($type)) {
            $type = $this->thousands_delim_type;
        }
        switch ($type) {
            case self::DELIM_SPACE:
                return $this->plugin->txt('delim_space');
            case self::DELIM_NONE:
                return $this->plugin->txt('delim_none');
            case self::DELIM_DOT:
            default:
                return $this->plugin->txt('delim_dot');
        }
    }
}