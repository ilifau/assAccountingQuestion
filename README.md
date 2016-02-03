Copyright (c) 2015 Institut fuer Lern-Innovation,
Friedrich-Alexander-Universitaet Erlangen-Nuernberg 
GPLv2, see LICENSE 

================================
ILIAS Accounting Question plugin
================================

Author:   Fred Neumann <fred.neumann@fim.uni-erlangen.de>
Version:  1.3.2 (2015-12-11)
Supports: ILIAS 5.0

Installation
------------

1. Copy the assAccountingQuestion directory to your ILIAS installation at the followin path 
(create subdirectories, if neccessary):
Customizing/global/plugins/Modules/TestQuestionPool/Questions/assAccountingQuestion

2. Go to Administration > Plugins

3. Choose "Update" for the assAccountingQuestion plugin
4. Choose "Activate" for the assAccountingQuestion plugin

There is nothing to configure for this plugin.

Usage
-----

This plugin provides a test question type for financial booking tables 
(t-accounts and booking records).

See Anleitung-Deutsch.pdf and Manual-English.pdf in docs/ for details.


===============
Version History
===============

Version 1.4.0 (2016-02-03)
--------------------------
* Initial import to GitHub
* set ILIAS version to 5.1.x
* fix handling of intermediate and authorized solution in ILIAS 5.1


Version 1.3.2 (2015-12-11)	IMPORTANT BUGFIX!
--------------------------
* Fixed setting of points to 0 when test results are recalculated


Version 1.3.1 (2015-11-23)
--------------------------
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


SVN Version 1.3.0 (2015-07-17)
------------------------------
* flash-free html5 user interface
* result calculation in php (instead of flash)


SVN Version 1.2.1 (2015-02-23)
------------------------------
* beta version for ILIAS 5.0
* updated version number and compatibility
* known bugs: ignored "bonus for correct order of bookings"
* todo: move the points calculation from Flash to PHP
* todo: get entirely rid of Flash


SVN Version 1.1.7 (2014-07-23)
------------------------------
* stable version for ILIAS 4.4
* support taxonomies in question pools
* supports feedback, hints and suggested solutions
  (note:, hints and suggested solutions are not exported/imported by ILIAS 4.4)
* supports export and import of images in the uestion text of partial questions

SVN Version 1.1.0 (2014-01-08)
------------------------------
* alpha version for ILIAS 4.4


SVN (2013-11-18)
----------------
* fixed comparing of Flash and ILIAS calculation in DEVMODE


Version 1.0.1 (2013-08-30)
--------------------------
* fixed copy, duplicate and sync procedures


Version 1.0.0 (2013-03-20)
------------------------

* First published version
* Known bugs: wrong calculation of points if more booking records are in one table
