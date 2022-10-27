ILIAS Accounting Question plugin
================================

Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg,  GPLv2, see LICENSE 

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

Since the core maintenance for ILIAS 5.4 has ended, this plugin version is no longer maintained, too. Please update ILIAS and this plugin to the newest versions.

* Version 1.8 for ILIAS 5.4 is available in https://github.com/ilifau/assAccountingQuestion/tree/master-ilias54
* Version 1.7 for ILIAS 5.3 is available in https://github.com/ilifau/assAccountingQuestion/tree/master-ilias53
* Version 1.5 for ILIAS 5.2 is available in https://github.com/ilifau/assAccountingQuestion/tree/master52+

Version 1.8.7 (2022-10-27)
* Solved Mantis #35055 (thx to mjansen for the PR)

Version 1.8.6 (2020-08-04)
* Optional Definition of thousands delimiter per question

Version 1.8.5 (2020-05-27)
--------------------------
* Definition of thousands delimiter for the presentation of centered and calculated values

Version 1.8.4 (2020-05-21)
--------------------------
* Usability changes regarding using TAB key to fill in first the left column, and after the right column, and improvements in how the question is presented to the user if using a Tablet or a Mobile device.

Version 1.8.3 (2019-10-28)
--------------------------
* Use precision also for calculated variables (substituted variables are reounded before)
* Suppress display of zeros in empty solution lines
* Upload and download of variables XML

Version 1.8.2 (2019-07-25)
--------------------------
* Support for Variables (see docs/Manual-English.pdf)

Version 1.8.0 (2019-06-09)
--------------------------
* Updated for ILAS 5.4

Version 1.7.0 (2018-09-17)
--------------------------
* Updated for ILIAS 5.3

Version 1.5.0 (2017-04-09)
--------------------------
* Updated for ILIAS 5.2

Version 1.4.0 (2016-02-03)
--------------------------
* Initial import to GitHub
* set ILIAS version to 5.1.x
* fix handling of intermediate and authorized solution in ILIAS 5.1
