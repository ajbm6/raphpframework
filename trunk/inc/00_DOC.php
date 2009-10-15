<?php
	/**
	 * RA PHP Framework :: DOCUMENTATION
	 *		
	 *		-- BEFORE you get started, you need to know some basic things like: "The Concepts", "The Directory Structure", 
	 * "The MOD Structure" and some other sections we're going to talk about. So, let's get started.
	 *
	 * ## -- ## The Concepts ## -- ##
	 *
	 *		-- The framework is based on concepts like: MVC, ORM and STH. The MVC (Model, View, Controller), the ORM (Object Relationship
	 * Mapping), the STH (Strong Type Hinting) - are concepts that enable us to work better.
	 *		-- For MVC, we provide the following CLASSES:
	 *			-- TPL (template) - used with methods like TPL::tpSet/TPL::tpExe to execute templates. You can check existing code in
	 *			modules like 'Articles', 'Faq' or the 'FRM' CLASS to see how these methods actually work.
	 *		-- For ORM, we provide the following CLASSES:
	 *			-- SQL (structured query language) - along with the doModuleTokens () methods that has a job to change query tokens to
	 *			actual field names, thus allowing us to have the FULL power of the ORM at our hands.
	 *			-- Also, we provide a ModuleClassName.cfg file that contains the actual pointer - field mapping and other configuration
	 * 			data needed for initialization.
	 *		-- For STH: as you've already seen, we provide all methods with type-hinted parameters. Each method only accepts types of the
	 * 		specified in the method definition. Given a type of parameter incompatible with the definition of the method, the PHP
	 *		error-handling mechanism kicks in reporting an error.
	 *
	 *	## -- ## The Directory Structure ## -- ##
	 *		We've organized the directory structure as following:
	 *			- The 00_CFG.php file contains the CONSTANTS used to define the names of the default directories. We use such a mapping
	 *			to allow for more control over the names of these folders. We'll be using the standards:
	 *				-- inc/: Needed CORE includes;
	 *				-- int/: Needed CORE language definitions;
	 *				-- frm/: Framework specific files. Like 'frm_gen_input.tp' - a generic template used to generate auto-forms.
	 *				-- adm/: A generic directory used with adm/index.php to CALL the mod/administration MODULE.
	 *				-- cch/: The CACHE directory. Must be protected by a [Deny ALL] .htaccess file usually.
	 *				-- log/: The error logging directory. Easier than searching through /var/log/apache/php_error_log as our loggin is
	 *				domain specific;
	 *				-- upd/: The upload directory. Organized as mod, having for example: mod/articles ---> upd/articles.
	 *				-- upd/tmp: The temporary upload directory.
	 *				-- dev/{hdr, ftr, oth} - Header/Footer INCLUDES (automatically through append_file)  and 'Others' (manually included);
	 *				-- mod/{name_of_mod}
	 *					-- The MODULE, organized as follows:
	 *						-- {name_of_mod}/adm/: administration file(s);
	 *						-- {name_of_mod}/cfg/: configuration file(s);
	 *						-- {name_of_mod}/inc/: the controller file(s);
	 *						-- {name_of_mod}/int/: language file(s);
	 *						-- {name_of_mod}/skn/{skin_name = default}: skin (template) directory, organized as follows:
	 *							-- {name_of_mod}/skn/{default}/css: The CSS directory, used with TPL::manageCSS to include such files;
	 *							-- {name_of_mod}/skn/{default}/jss: The JSS directory, used with TPL::manageJSS to include such files;
	 *							-- {name_of_mod}/skn/{default}/img: The IMG directory, used to store template images and other resources;
	 *							-- and the root directory containing *.tp files used to execute parts (widgets) of the specified MODULE;
	 *
	 *		That's about it. We've provided other MODULES by default that you can use and check-out to see how they are organized. You
	 * can follow the same path in development as we've already done much of the work for you, but you are not restricted to go for a
	 * different way (if you feel it's better).
	 *
	 * ## -- ## The MOD Structure ## -- ##
	 *
	 *		MODULES are independent pieces of code that do just a little bunch of things. Like 'Articles' provide a mechanism for
	 * having categories and publishing all types of articles, 'Products' provide a mechanism to manage producs in a DATABASE.
	 *
	 * 		When going INSIDE of a MODULE you will find:
	 *			-- /cfg/: containint MOD.cfg a file which sets the CLASS name to be used upon instantiation and mod_is_active = 1;
	 *			And, the {ClassNameOfModule}.cfg file containing specific mappings between fields in the table and object pointers as 
	 *			with some default configuration data;
	 *			-- /inc/: containing the controller;
	 *			-- /int/: containing the language definitions;
	 *			-- /skn/: containing the SKIN (templates) file and other resources;
	 *
	 *
	 *		That's ALL. The code should be pretty CLEAR from now on.
	 */
?>
