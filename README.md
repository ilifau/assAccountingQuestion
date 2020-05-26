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
* Version 1.8 for ILIAS 5.4 is maintained in https://github.com/ilifau/assAccountingQuestion/tree/master-ilias54
* Version 1.7 for ILIAS 5.3 is maintained in https://github.com/ilifau/assAccountingQuestion/tree/master-ilias53
* Version 1.5 for ILIAS 5.2 is maintained in https://github.com/ilifau/assAccountingQuestion/tree/master52+

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

Version 1.7.3 (2019-10-28)
--------------------------
* Use precision also for calculated variables (substituted variables are reounded before)
* Suppress display of zeros in empty solution lines
* Upload and download of variables XML

Version 1.7.2 (2019-07-30)
--------------------------
* Support for Variables (see docs/Manual-English.pdf)

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
