ILIAS Accounting Question plugin
================================

Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg,  GPLv2, see LICENSE 

- Author: Fred Neumann <fred.neumann@ili.fau.de>, Jesus Copado <jesus.copado@ili.fau.de>
- Forum: http://www.ilias.de/docu/goto_docu_frm_3474_1944.html
- Bug Reports: http://www.ilias.de/mantis (Choose project "ILIAS plugins" and filter by category "Accounting Question")

Installation
------------
When you download the Plugin as ZIP file from GitHub, please rename the extracted directory to *assAccountingQuestion* (remove the branch suffix, e.g. -master).

1. Copy the assAccountingQuestion directory to your ILIAS installation at the following path 
(create subdirectories, if neccessary):
Customizing/global/plugins/Modules/TestQuestionPool/Questions/
2. Go to Administration > Plugins
3. Choose "Update" for the assAccountingQuestion plugin
4. Choose "Activate" for the assAccountingQuestion plugin

There is nothing to configure for this plugin.

Usage
-----
This plugin provides a test question type for financial booking tables  (t-accounts and booking records).

See [Anleitung](docs/Anleitung-Deutsch.pdf) or [Manual](docs/Manual-English.pdf) for details.

Version History
===============

* All versions for ILIAS 5.1 and higher are maintained in GitHub: https://github.com/ilifau/assAccountingQuestion
* Former versions for ILIAS 5.0 and lower are maintained in ILIAS SVN: http://svn.ilias.de/svn/ilias/branches/fau/plugins

Version 1.4.0 (2016-02-03)
--------------------------
* Initial import to GitHub
* set ILIAS version to 5.1.x
* fix handling of intermediate and authorized solution in ILIAS 5.1

Version 1.3.2 (2015-12-11)
--------------------------
* http://svn.ilias.de/svn/ilias/branches/fau/plugins/assAccountingQuestion-1.3.x 
* stable version for ILIAS 5.0
* IMPORTANT BUGFIX: Fixed setting of points to 0 when test results are recalculated

Version 1.3.1 (2015-11-23)
--------------------------
* flash-free html5 user interface
* result calculation in php (instead of flash)
* IMPORTANT: Multiple booking record per table are not longer supported.
             Please use multiple booking tables per question instead
* Abandoned the settings "bestanden_ab" and "debug" in booking tables.
  These are obsolete in the flash-free version.
* Abandoned the obsolete settings "bonus_reihe" and "malus_anzahl" for booking records.
  These are obsolete as multiple records per table are not longer supported.
* Fixed the calculation of malus points in ILIAS to be exactly the same as in flash.
* Updated the manuals (with precise description of the points calculation).
* Better support for preview and print view.
* Suppressed the auto-complete for characters in drop-downs for accounts.
  This prevents a blind "guessing" of account names by the students.
* Replaced the textual result presentation by an html table.
* Better output of detailed results in Excel export of evaluation data.
* Multiple internal cleanups.

Version 1.1.7 (2014-07-23)
--------------------------
* http://svn.ilias.de/svn/ilias/branches/fau/plugins/assAccountingQuestion-1.1.x 
* stable version for ILIAS 4.4
* support taxonomies in question pools
* supports feedback, hints and suggested solutions
  (note:, hints and suggested solutions are not exported/imported by ILIAS 4.4)
* supports export and import of images in the uestion text of partial questions

Version 1.0.1 (2013-08-30)
--------------------------
* http://svn.ilias.de/svn/ilias/branches/fau/plugins/assAccountingQuestion-1.0.x
* stable version for ILIAS 4.3
* fixed copy, duplicate and sync procedures
