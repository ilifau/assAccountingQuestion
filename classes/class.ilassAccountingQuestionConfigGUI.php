<?php
/**
 * Copyright (c) 2020 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */
class ilassAccountingQuestionConfigGUI extends ilPluginConfigGUI
{
    /** @var  ilAccessHandler $access */
    protected $access;

    /** @var ilCtrl $ctrl */
    protected $ctrl;

    /** @var  ilLanguage $lng */
    protected $lng;

    /** @var ilTabsGUI */
    protected $tabs;

    /** @var  ilToolbarGUI $toolbar */
    protected $toolbar;

    /** @var ilTemplate $tpl */
    protected $tpl;

    /** @var ilassAccountingQuestionPlugin $plugin */
    protected $plugin;

    /** @var  assAccountingQuestionConfig $config */
    protected $config;

    /**
     * Constructor.
     */
    public function __construct()
    {
        global $DIC;

        $this->access = $DIC->access();
        $this->ctrl = $DIC->ctrl();
        $this->lng = $DIC->language();
        $this->tabs = $DIC->tabs();
        $this->toolbar = $DIC->toolbar();
        $this->tpl = $DIC['tpl'];

        $this->lng->loadLanguageModule('assessment');
    }

    /**
     * Handles all commands, default is "configure"
     */
    public function performCommand($cmd)
    {
        $this->plugin = $this->getPluginObject();
        $this->config = $this->plugin->getConfig();

        switch ($cmd)
        {
            case "saveConfiguration":
                $this->saveConfiguration();
                break;

            case "configure":
            default:
                $this->editConfiguration();
                break;
        }
    }

    /**
     * Edit the configuration
     */
    protected function editConfiguration()
    {
        $form = $this->initConfigForm();
        $this->tpl->setContent($form->getHTML());
    }

    /**
     * Save the edited configuration
     */
    protected function saveConfiguration()
    {
        $form = $this->initConfigForm();
        if (!$form->checkInput())
        {
            $form->setValuesByPost();
            $this->tpl->setContent($form->getHTML());
            return;
        }

        $this->config->thousands_delim_type = $form->getInput('thousands_delim_type');
        $this->config->save();

        ilUtil::sendSuccess($this->plugin->txt('settings_saved'), true);
        $this->ctrl->redirect($this, 'editConfiguration');
    }

    /**
     * Fill the configuration form
     * @return ilPropertyFormGUI
     */
    protected function initConfigForm()
    {
        $form = new ilPropertyFormGUI();
        $form->setFormAction($this->ctrl->getFormAction($this, 'editConfiguration'));
        $form->setTitle($this->plugin->txt('plugin_configuration'));

        $td = new ilSelectInputGUI($this->plugin->txt('thousands_delim_type'), 'thousands_delim_type');
        $td->setInfo($this->plugin->txt('thousands_delim_type_info'));
        $td->setOptions(array(
            assAccountingQuestionConfig::DELIM_NONE => $this->plugin->txt('delim_none'),
            assAccountingQuestionConfig::DELIM_DOT => $this->plugin->txt('delim_dot'),
            assAccountingQuestionConfig::DELIM_SPACE => $this->plugin->txt('delim_space'),
        ));
        $td->setValue($this->config->thousands_delim_type);
        $form->addItem($td);

        $form->addCommandButton('saveConfiguration', $this->lng->txt('save'));
        return $form;
    }
}