#v0.9.5

* Fixed issue in version 8 and later, where if "remote updates" are applied for the concrete5 core, it would search in the wrong directory;
* "ucFirst" function call fixed to "ucfirst" - it isn't a case-sensitive PHP function call; 

#v0.9.4

* Updated to work 100% with concrete5 version 8.x (due to the new Express Entities and such);

#v0.9.3

* Updated file indenting/formatting, cleaned up code and such;
* Replaced (forgotten) deprecated Database function "get" with "connection";
* Replaced deprecated Database function "getCol" with "fetchAll";
* Removed unneeded "uninstall" function;

#v0.9.2

* Fixed namespaces for newer versions of concrete5;
* Removed unused "use" statements;
* Rewritten deprecated concrete5 core code (Loader::helper(), Database::get(), Database::GetAll() etc.);

#v0.9.1

* For developers: Made classes/controllers a bit more PSR compliant

#v0.9.0

* Initial Release