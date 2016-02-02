/* Copyright (c) 1998-2015 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Accounting question functions
 */
il.AccountingQuestion = new function() {

    /**
     * @var object      self reference for usage in event handlers
     */
    var self = this;

    /**
     * @var object      configuration settings
     */
    var config = {
       nameMatching:    false            // allow a (partial) matching of account names
    };

    /**
     * Initialize
     */
    this.init = function(a_config) {
        self.config = a_config;

        $('.ilAccqstAccount').combobox();

        // prevent searching by names
        if (!self.config.nameMatching) {
            $('.ilAccqstAccount').keypress(self.isNumber);
        }

        // prevent character input in amout fields
        $('.ilAccqstAmount').keypress(self.isNumber);
    };


    this.isNumber = function(evt) {

        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 44) {
            return false;
        }
        return true;
    };
};
