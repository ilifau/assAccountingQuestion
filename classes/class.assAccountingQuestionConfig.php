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
    }


    /**
     * Save the configuration
     */
    public function save()
    {
        $this->settings->set('thousands_delim_type', (string) $this->thousands_delim_type);
    }

    /**
     * Get the effective the thousands delimiter
     */
    public function getThousandsDelim() {
        switch ($this->thousands_delim_type) {
            case self::DELIM_SPACE:
                return ' ';
            case self::DELIM_NONE:
                return '';
            case self::DELIM_DOT:
            default:
                return '.';
        }
    }
}