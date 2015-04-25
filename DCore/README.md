DCore
-----

DCore is a simple lightweight PHP framework. DCore was never intended to be a 
replacement for other frameworks, but has grown to maturity and felt sharing it 
would not hurt. Plus would force for better code standards and documentation.

Second motive is to create a XHP UI framework. The XHP framework would site on top
of DCore and reply on it. DCore would not depend on the XHP framework.

Can try the [tutorials](http://dcode.bbfmedia.com/docs/tutorials.md)


Status
=======

Currently many items need to be worked on. 

Unit testing is not in the repository but I hope to add them soon.




DCore Archetcture
================
The code is light weight enough that on could step through the execution of the sample 
application and have a good understanding of the entire project.

Below is a description of the execution made by a DCore apps. "*" files are files in the
application not in the framework.

```
*index.php
 |  DCore               includes DCore 
 |  |                   DCore include *'config.php' in you application
 |  |                   and creates singlton global _$registry_ global
 |  |
 |  |   $registry       calls $register->load() to load all modules defined in *_config.php_
 |      |               $register->load() loads each module such as cache, router 
 |      |   module      by calling module->init();
 |      |   
 |      |   plugins     if plugins is a module in your config then plugins->init() is called
 |          |           and will include each plugin witheither %plugin%\init.php is included 
 |          |           or create the plugin class
 |  |   |               include *init.php
 |
 |  router              *index.php call $registry->router->loader() 
 |  |                   takes url and finds controler and action
 |  |   *controller     router calls the controller->$action()
 |      |               controller does contoller things:)
 |
 |  template            your *index.php can call a view choice 
 |  |                   by calling template which intern finds the view
 |  |
 |  |    view            renders view              
 |
 |                      index.php eachos out the view
```