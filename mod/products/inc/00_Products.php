<?php
/*
 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

############# Motto: "Batteries not included!";
class Products extends ICommonExtension implements IFaceCommonConfigExtension {
    /* OBJECT: Identity */
    protected static $objName                   = 'Products :: RA PHP Framework';
    protected $objIdentificationString          = __CLASS__;

    /* MPTT for categories */
    protected static $objMPTT                   = NULL;

    /* TABLE: Products */
    public static $objProductsTable             = NULL;
    public static $objProductsTableFId          = NULL;
    public static $objProductsTableFCode        = NULL;
    public static $objProductsTableFName        = NULL;
    public static $objProductsTableFSEO         = NULL;
    public static $objProductsTableFPDF         = NULL;
    public static $objProductsTableFURL         = NULL;
    public static $objProductsTableFDescription = NULL;
    public static $objProductsTableFPrice       = NULL;
    public static $objProductsTableFStoc        = NULL;
    public static $objProductsTableFCategoryId  = NULL;

    /* TABLE: Categories */
    public static $objCategoryTable             = NULL;
    public static $objCategoryTableFId          = NULL;
    public static $objCategoryTableFName        = NULL;
    public static $objCategoryTableFSEO         = NULL;
    public static $objCategoryTableFDescription = NULL;
    public static $objCategoryTableFDate        = NULL;

    /* TABLE: Images for products */
    public static $objProductsIMGTable          = NULL;
    public static $objProductsIMGTableFId       = NULL;
    public static $objProductsIMGTableFProdId   = NULL;
    public static $objProductsIMGTableFTitle    = NULL;
    public static $objProductsIMGTableFURL      = NULL;
    public static $objProductsIMGTableFCaption  = NULL;

    /* TABLE: Properties for products */
    public static $objProductsPropertyTable     = NULL;
    public static $objProductsPropertyTableFId  = NULL;
    public static $objProductsPropertyTableFPId = NULL;
    public static $objProductsPropertyTableFKey = NULL;
    public static $objProductsPropertyTableFVar = NULL;

    public static $objItemsPerPage              = NULL;

    # CONSTRUCT;
    public function __construct () {
        // Construct any possible parent;
        parent::__construct ();
        // Do the tie ...
        $this->tieInCommonConfiguration ();

        // Set the proper configuration options, from the config file;
        self::$objProductsTable                 = $this->getConfigKey (new S ('products_table'));
        self::$objProductsTableFId              = $this->getConfigKey (new S ('products_table_field_id'));
        self::$objProductsTableFCode            = $this->getConfigKey (new S ('products_table_field_code'));
        self::$objProductsTableFName            = $this->getConfigKey (new S ('products_table_field_name'));
        self::$objProductsTableFSEO             = $this->getConfigKey (new S ('products_table_field_seo'));
        self::$objProductsTableFPDF             = $this->getConfigKey (new S ('products_table_field_pdf'));
        self::$objProductsTableFURL             = $this->getConfigKey (new S ('products_table_field_url'));
        self::$objProductsTableFDescription     = $this->getConfigKey (new S ('products_table_field_description'));
        self::$objProductsTableFPrice           = $this->getConfigKey (new S ('products_table_field_price'));
        self::$objProductsTableFStoc            = $this->getConfigKey (new S ('products_table_field_stoc'));
        self::$objProductsTableFCategoryId      = $this->getConfigKey (new S ('products_table_field_category_id'));

        // Categories ...
        self::$objCategoryTable                 = $this->getConfigKey (new S ('products_table_categories'));
        self::$objCategoryTableFId              = $this->getConfigKey (new S ('products_table_categories_field_id'));
        self::$objCategoryTableFName            = $this->getConfigKey (new S ('products_table_categories_field_name'));
        self::$objCategoryTableFSEO             = $this->getConfigKey (new S ('products_table_categories_field_seo'));
        self::$objCategoryTableFDescription     = $this->getConfigKey (new S ('products_table_categories_field_description'));
        self::$objCategoryTableFDate            = $this->getConfigKey (new S ('products_table_categories_field_date'));

        // Images for products ...
        self::$objProductsIMGTable              = $this->getConfigKey (new S ('products_table_images'));
        self::$objProductsIMGTableFId           = $this->getConfigKey (new S ('products_table_images_field_id'));
        self::$objProductsIMGTableFProdId       = $this->getConfigKey (new S ('products_table_images_field_product_id'));
        self::$objProductsIMGTableFTitle        = $this->getConfigKey (new S ('products_table_images_field_title'));
        self::$objProductsIMGTableFURL          = $this->getConfigKey (new S ('products_table_images_field_url'));
        self::$objProductsIMGTableFCaption      = $this->getConfigKey (new S ('products_table_images_field_caption'));

        // Properties for product ...
        self::$objProductsPropertyTable         = $this->getConfigKey (new S ('products_table_properties'));
        self::$objProductsPropertyTableFId      = $this->getConfigKey (new S ('products_table_properties_field_id'));
        self::$objProductsPropertyTableFPId     = $this->getConfigKey (new S ('products_table_properties_field_product_id'));
        self::$objProductsPropertyTableFKey     = $this->getConfigKey (new S ('products_table_properties_field_key'));
        self::$objProductsPropertyTableFVar     = $this->getConfigKey (new S ('products_table_properties_field_var'));

		// Configuration ...
        self::$objItemsPerPage = new S ('10');

        // Load'em defaults ... ATH, STG and others ...
        $this->ATH = MOD::activateModule (new FilePath ('mod/authentication'), new B (TRUE));
        $this->STG = MOD::activateModule (new FilePath ('mod/settings'), new B (TRUE));

        // DB: Auto-CREATE:
        $objQueryDB = new FileContent ($this->getPathToModule ()->toRelativePath () .
        _S . CFG_DIR . _S .  __CLASS__ . SCH_EXTENSION);

        // Make a FOREACH on each ...
        foreach (_S ($objQueryDB->toString ())
        ->fromStringToArray (RA_SCHEMA_HASH_TAG) as $k => $v) {
            // Make'em ...
            $this->_Q (_S ($v));
        }

		// Get an MPTT Object, build the ROOT, make sure the table is OK;
        self::$objMPTT = new MPTT (self::$objCategoryTable,
        MPTT::mpttAddUnique (new S (__CLASS__),
        new S ((string) $_SERVER['REQUEST_TIME'])));
    }

    /**
     * Will replace module tokens (also named table fields) that can be used independent of the table structure;
     *
     * This method, will replace a series of table field tokens, with their respective fields. This is to allow a better way to
     * write SQL conditions from the front-end of the module, that will interact perfectly with the back-end of the current
     * module which should make the MVC pattern as pure as possible;
     *
     * @param S $objSQLParam The SQL string to be processed;
     * @return S Will return the current SQL string with modified tokens;
     */
    public function doModuleToken (S $objSQLParam) {
        // Set the tokens to be replaced;
        $objTokens = new A;
        $objTokens[1]   = 'objProductsTable';
        $objTokens[2]   = 'objProductsTableFId';
        $objTokens[3]   = 'objProductsTableFCode';
        $objTokens[4]   = 'objProductsTableFName';
        $objTokens[5]   = 'objProductsTableFSEO';
        $objTokens[6]   = 'objProductsTableFPDF';
        $objTokens[7]   = 'objProductsTableFURL';
        $objTokens[8]   = 'objProductsTableFDescription';
        $objTokens[9]   = 'objProductsTableFPrice';
        $objTokens[10]  = 'objProductsTableFStoc';
        $objTokens[11]  = 'objProductsTableFCategoryId';
        $objTokens[12]  = 'objCategoryTable';
        $objTokens[13]  = 'objCategoryTableFId';
        $objTokens[14]  = 'objCategoryTableFName';
        $objTokens[15]  = 'objCategoryTableFSEO';
        $objTokens[16]  = 'objCategoryTableFDescription';
        $objTokens[17]  = 'objProductsIMGTable';
        $objTokens[18]  = 'objProductsIMGTableFId';
        $objTokens[19]  = 'objProductsIMGTableFProdId';
        $objTokens[20]  = 'objProductsIMGTableFTitle';
        $objTokens[21]  = 'objProductsIMGTableFURL';
        $objTokens[22]  = 'objProductsIMGTableFCaption';
        $objTokens[23]  = 'objProductsPropertyTable';
        $objTokens[24]  = 'objProductsPropertyTableFId';
        $objTokens[25]  = 'objProductsPropertyTableFPId';
        $objTokens[26]  = 'objProductsPropertyTableFKey';
        $objTokens[27]  = 'objProductsPropertyTableFVar';
        $objTokens[28]  = 'objCategoryTableFDate';

        // Set the replacements;
        $objReplac = new A;
        $objReplac[1]   = self::$objProductsTable;
        $objReplac[2]   = self::$objProductsTableFId;
        $objReplac[3]   = self::$objProductsTableFCode;
        $objReplac[4]   = self::$objProductsTableFName;
        $objReplac[5]   = self::$objProductsTableFSEO;
        $objReplac[6]   = self::$objProductsTableFPDF;
        $objReplac[7]   = self::$objProductsTableFURL;
        $objReplac[8]   = self::$objProductsTableFDescription;
        $objReplac[9]   = self::$objProductsTableFPrice;
        $objReplac[10]  = self::$objProductsTableFStoc;
        $objReplac[11]  = self::$objProductsTableFCategoryId;
        $objReplac[12]  = self::$objCategoryTable;
        $objReplac[13]  = self::$objCategoryTableFId;
        $objReplac[14]  = self::$objCategoryTableFName;
        $objReplac[15]  = self::$objCategoryTableFSEO;
        $objReplac[16]  = self::$objCategoryTableFDescription;
        $objReplac[17]  = self::$objProductsIMGTable;
        $objReplac[18]  = self::$objProductsIMGTableFId;
        $objReplac[19]  = self::$objProductsIMGTableFProdId;
        $objReplac[20]  = self::$objProductsIMGTableFTitle;
        $objReplac[21]  = self::$objProductsIMGTableFURL;
        $objReplac[22]  = self::$objProductsIMGTableFCaption;
        $objReplac[23]  = self::$objProductsPropertyTable;
        $objReplac[24]  = self::$objProductsPropertyTableFId;
        $objReplac[25]  = self::$objProductsPropertyTableFPId;
        $objReplac[26]  = self::$objProductsPropertyTableFKey;
        $objReplac[27]  = self::$objProductsPropertyTableFVar;
        $objReplac[28]  = self::$objCategoryTableFDate;

        // Do a CALL to the parent, make it tokenize;
        return parent::doModuleTokens ($objTokens, $objReplac, $objSQLParam);
    }

    /**
     * Will add the administration menu;
     *
     * This method will tie in the current module with the administration module, while adding the proper administrator links.
     * The files to be required by the administration module are set in the configuration file of this module.
     *
     * @param IFaceAdministration $objAdministrationMech The administration object;
     * @return void Doesn't need to return anything;
     */
    public function tieInWithAdministration (IFaceAdministration $objAdministrationMech) {
        // Do a CALL to the parent ...
        parent::tieInWithAdministration ($objAdministrationMech);

        // Do the administration menu;
        $objWP = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
        $this->getConfigKey (new S ('products_file_dashboard')));
        self::$objAdministration->setMenuLink (new S (MANAGE_PRODUCTS), $objWP,
        $this->getHELP (new S (MANAGE_PRODUCTS)));

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Products.Products.Do.View');
        $objACL[] = new S ('Products.Categories.Do.View');
        $objACL[] = new S ('Products.Do.Operations');
        $objACL[] = new S ('Products.Do.Configuration');

        // ONLY: Products.Products.Do.View;
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[0])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('products_file_manage_products')));
            self::$objAdministration->setSubMLink (new S (MANAGE_PRODUCTS), $objMF,
            $this->getHELP (new S (MANAGE_PRODUCTS)));
        }

        // ONLY: Products.Categories.Do.View;
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[1])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('products_file_manage_categories')));
            self::$objAdministration->setSubMLink (new S (MANAGE_PRODUCT_CATEGORIES), $objMF,
            $this->getHELP (new S (MANAGE_PRODUCT_CATEGORIES)));
        }

        // ONLY: Products.Do.Operations;
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[2])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('products_file_manage_operations')));
            self::$objAdministration->setSubMLink (new S (MANAGE_PRODUCT_OPERATIONS), $objMF,
            $this->getHELP (new S (MANAGE_PRODUCT_OPERATIONS)));
        }

        // ONLY: Products.Do.Configuration;
        if ($this->ATH->checkCurrentUserZoneACL ($objACL[3])->toBoolean () == TRUE) {
            $objMF = new FilePath ($this->getPathToAdmin ()->toRelativePath () .
            $this->getConfigKey (new S ('products_file_manage_configuration')));
            self::$objAdministration->setSubMLink (new S (MANAGE_PRODUCT_CONFIGURATION), $objMF,
            $this->getHELP (new S (MANAGE_PRODUCT_CONFIGURATION)));
        }

        // WIDGET: Statistics for products ...
        self::$objAdministration->setWidget ($this->getHELP (new S ('adminStatistics'))
        ->doToken ('%p', $this->getProductCount ())
        ->doToken ('%c', $this->getCategoryCount ()));
    }

    /**
     * Will tie in with the authentication mechanism;
     *
     * This method will tie in with the authentication mechanism, making necessary zones, and binding this module with the
     * authentication module making necessary linkes along the way. For most of the time it's used as a mapping tool for the
     * default administrator group and necessary zones;
     *
     * @param IFaceAuthentication $objAuthenticationMech The authentication mechanism;
     */
    public function tieInWithAuthentication (IFaceAuthentication $objAuthenticationMech) {
    	// Do a CALL to the parent ...
        parent::tieInWithAuthentication ($objAuthenticationMech);

        // Set ACLs;
        $objACL = new A;
        $objACL[] = new S ('Products.Products.Do.View');
        $objACL[] = new S ('Products.Categories.Do.View');
        $objACL[] = new S ('Products.Do.Operations');
        $objACL[] = new S ('Products.Do.Configuration');

        // Do a FOREACH ... on each ...
        foreach ($objACL as $k => $v) {
            if ($this->ATH->checkZoneByName ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMakeZone ($objACL[$k], $this->getObjectCLASS ());

            if ($this->ATH->checkAdministratorIsMappedToZone ($objACL[$k])->toBoolean () == FALSE)
            $this->ATH->doMapAdministratorToZone ($objACL[$k]);
        }
    }

    /**
     * Will tie the current object with the frontend object;
     *
     * This method, given the Frontend object will tie the given object with it. It's a way we can make specific code execute
     * upon instantiating those objects in the frontend of the webiste. Almost the same principle we apply on the backend, but
     * with a minor twist in it (as we don't automatically do it for the front);
     *
     * @param $objFrontendObject The frontend object;
     * @return mixed God knows what ...
     */
    public function tieInWithFrontend (Frontend $objFrontendObject) {
        // Do a CALL to the parent;
        parent::tieInWithFrontend ($objFrontendObject);

        // JSS ...
        TPL::manageJSS (new FilePath ($this->getPathToSkinJSS ()->toRelativePath () .
        $this->getObjectCLASS ($objFrontendObject) . JSS_EXTENSION), new S (__CLASS__));
    }

    /**
     * Will check that the category URL is unique;
     *
     * This method will check that the category URL is unique and return true if it is. This way we are sure that no two
     * have the same URL which would lead to a redirect loop inside our system. Sadly, this is the case, that we must be sure we
     * don't have two categories with the same URL.
     *
     * @param S $objCategoryURL The category URL to check;
     * @return boolean Will return true if the URL is unique;
     */
    public function checkCategoryURLIsUnique (S $objCategoryURL) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objCategoryTableFSEO'))->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryURL))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check that the given product code is unique;
     *
     * This method, given the code to check will make sure that the code is unique in our system. Every product, and every
     * product name should be unique, so it can identify the exact product the user has payed for. If something happens and the
     * code is not unique, errors can arrise from weird places;
     *
     * @param S $objCheckedCode The code to check for unicity;
     * @return boolean Will return true if the code is unique;
     */
    public function checkCodeIsUnique (S $objCheckedCode) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objProductsTableFCode'))->doToken ('%table', self::$objProductsTable)
        ->doToken ('%condition', new S ('WHERE %objProductsTableFCode = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCheckedCode))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check that the product name is unique;
     *
     * This method, given a name will check that the given product name is unique. We need an unique product name, in combination
     * with an unique product code just to be sure we can identify each product by two distinct characeristics. Also, this allows
     * us to have proper URLs, that are unique system-wide;
     *
     * @param S $objProductName The product name;
     * @return boolean Will return true if the product name is unique;
     */
    public function checkProductNameIsUnique (S $objProductName) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objProductsTableFName'))->doToken ('%table', self::$objProductsTable)
        ->doToken ('%condition', new S ('WHERE %objProductsTableFName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objProductName))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will check that the product URL is unique;
     *
     * This method will check that the URL we store for each product is unique; This allow us to check that products that seem
     * to have the same name (usually this is a rare case) have a different generated URL. It can happen that the name differs
     * by some special character, case in which we need to force the user to enter a slightly different name so we can generate
     * a proper, usable URL for that product;
     *
     * @param S $objProductURL The product URL to check for;
     * @return boolean Will return true if the product URL is unique;
     */
    public function checkProductURLIsUnique (S $objProductURL) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('%objProductsTableFSEO'))->doToken ('%table', self::$objProductsTable)
        ->doToken ('%condition', new S ('WHERE %objProductsTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objProductURL))->doCount ()->toInt () == 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will return the product count;
     *
     * This method will return the count of all products defined in the system, or if we pass and SQL condition, the count of
     * products that have met the SQL condition will be returned; This way, if we do a search for example, we could find-out
     * which products have met the search criteria;
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return integer Will return the count of the products that meet the SQL condition;
     */
    public function getProductCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) AS count'))->doToken ('%table', self::$objProductsTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the image count of the given product id.
     *
     * This method allows you to add an extra SQL condition to the query, something you can define and use at a later time,
     * thus giving you an extra management power, and not limiting you to some predefined SQL schema; This method is mainly
     * used to determine the images defined for a product id, like all other methods of this type take either a SEO url, or the
     * product name. Most of the uses here will be needed in the administration interface;
     *
     * @param $objProductId The product id to query for;
     * @param $objSQLCondition Another SQL condition, appeneded to the one we have;
     * @return integer Will return the count of the images that meet the SQL condition;
     */
    public function getImageCountByProductId (S $objProductId, S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) as count'))->doToken ('%table', self::$objProductsIMGTable)
        ->doToken ('%condition', new S ('WHERE %objProductsIMGTableFProdId = "%Id" %condition'))
        ->doToken ('%Id', $objProductId)->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the count of product properties of the given id;
     *
     * This method, given a product id and an additional SQL condition will return the count of product properties defined for
     * that specific product id. The only use of this method can be found only in the administrator interface, for pagination
     * purposes only;
     *
     * @param $objProductId The given product id;
     * @param $objSQLCondition The passed SQL condition;
     * @return itneger Will returnt he count of the properties that meet the SQL condition;
     */
    public function getPropertyCountByProductId (S $objProductId, S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) as count'))->doToken ('%table', self::$objProductsPropertyTable)
        ->doToken ('%condition', new S ('WHERE %objProductsPropertyTableFPId = "%Id" %condition'))
        ->doToken ('%Id', $objProductId)->doToken ('%condition', $objSQLCondition))
        ->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return the category count;
     *
     * This method will return the category count, based on the passed SQL condition. This way we can determine for example the
     * number of categories in the database, or if we do a search, and we need to determine the count of that search, we need
     * to pass it a proper SQL condition;
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return interger Will return the count of the categories that meet the SQL condition;
     */
    public function getCategoryCount (S $objSQLCondition = NULL) {
        // Do return ...
        return new I ((int) $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('COUNT(*) AS count'))->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', $objSQLCondition))->offsetGet (0)->offsetGet ('count')->toString ());
    }

    /**
     * Will return an array of products that meet the SQL condition;
     *
     * This method will return the array of products that meet the passed SQL condition. This way we are certain that we will
     * return a limited amount of products that we can use.
     *
     * @param S $objSQLCondition The passed SQL condition;
     * @return array The result array;
     */
    public function getProducts (S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('t1.*'))->doToken ('%table', self::$objProductsTable)
        ->doToken ('%condition', $objSQLCondition->prependString (_SP)->prependString ('AS t1')));
    }

    public function getProductsByPage (S $objPageInt, S $objOrdering = NULL) {
        // Do return ...
        return $this->getProducts (_S ('ORDER BY %objProductsTableFId %TypeOfOrdering LIMIT %LowerLimit, %UpperLimit')
        ->doToken ('%LowerLimit', ((int) $objPageInt->toString () - 1) * (int) self::$objItemsPerPage->toString ())
        ->doToken ('%UpperLimit', (int) self::$objItemsPerPage->toString ())
        ->doToken ('%TypeOfOrdering', $objOrdering == NULL ? new S ('DESC') : $objOrdering));
    }

    /**
     * Will return the images array of the given product id;
     *
     * This method, given a product ID and an optional SQL condition, will return the array of images defined for that product,
     * which meet the given SQL criteria. This method for example can be used in the administrator interface to generate the
     * table of images defined for a product;
     *
     * @param $objProductId The product ID to query for;
     * @param $objSQLCondition The appended SQL condition;
     * @return array Will return the result array;
     */
    public function getImagesByProductId (S $objProductId, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objProductsIMGTable)
        ->doToken ('%condition', new S ('WHERE %objProductsIMGTableFProdId = "%Id" %condition'))
        ->doToken ('%Id', $objProductId)->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return the images array of the given product URL;
     *
     * This method, given a product URL and an optional SQL condition, will return the array of images defined for that product,
     * which meet the given SQL criteria. This method for example can be used in the administrator interface to generate the
     * table of images defined for a product;
     *
     * @param $objProductURL The product URL to query for;
     * @param $objSQLCondition The appended SQL condition;
     * @return array Will return the result array;
     */
    public function getImagesByProductURL (S $objProductURL, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getImagesByProductId ($this->getProductInfoByURL ($objProductURL,
        self::$objProductsTableFId), $objSQLCondition);
    }

    /**
     * Will return the product properties of the given product id;
     *
     * This method will return the array of defined properties on a given product id. These properties can be used and shown in
     * a table of the given product. Also, they can be used in a comparison window where the user can see each property against
     * each other;
     *
     * @param $objProductId The product id to query for;
     * @param $objSQLCondition The passed SQL condition;
     * @return array Will return the result array;
     */
    public function getPropertiesByProductId (S $objProductId, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', self::$objProductsPropertyTable)
        ->doToken ('%condition', new S ('WHERE %objProductsPropertyTableFPId = "%Id" %condition'))
        ->doToken ('%Id', $objProductId)->doToken ('%condition', $objSQLCondition));
    }

    /**
     * Will return the product properties of the given product URL;
     *
     * This method will return the array of defined properties on a given product URL. These properties can be used and shown in
     * a table of the given product. Also, they can be used in a comparison window where the user can see each property against
     * each other;
     *
     * @param $objProductId The product URL to query for;
     * @param $objSQLCondition The passed SQL condition;
     * @return array Will return the result array;
     */
    public function getPropertiesByProductURL (S $objProductURL, S $objSQLCondition = NULL) {
        // Do return ...
        return $this->getPropertiesByProductId ($this->getProductInfoByURL ($objProductURL,
        self::$objProductsTableFId), $objSQLCondition);
    }

    /**
     * Will return the array of categories, if given a sub-category or not, with a given condition or not. The combination of
     * two factors can make the array be returned empty, depends much on what the developer asks the MPTT object to do.
     *
     * @param S $objSubCategory The sub-category to start getting sub-categories for;
     * @param S $objSQLCondition The SQL condition, ASC or DESC ordering, or any other appended SQL condition;
     * @return array The array of returned categories/subcategories;
     */
    public function getCategories (S $objSQLCondition = NULL, S $objSubCategory = NULL) {
        // Do return ...
        return self::$objMPTT->mpttGetTree ($objSubCategory, $objSQLCondition);
    }

    /**
     * Will return a field related to the given product id;
     *
     * This method, given a product id and a specific field to get, will retrieve that field from the database. It's a nice way,
     * knowing the id of the product to get information about any defined field;
     *
     * @param S $objProductId The product id to query for;
     * @param S $objFieldToGet The field to get;
     * @return mixed Depends on what was requested;
     */
    public function getProductInfoById (S $objProductId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objProductsTable)
        ->doToken ('%condition', new S ('WHERE %objProductsTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objProductId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return a field related to the given product name;
     *
     * This method, given a product name and a specific field to get, will retrieve that field from the database. It's a nice way,
     * knowing the name of the product to get information about any defined field;
     *
     * @param S $objProductName The product name to query for;
     * @param S $objFieldToGet The field to get;
     * @return mixed Depends on what was requested;
     */
    public function getProductInfoByName (S $objProductName, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objProductsTable)
        ->doToken ('%condition', new S ('WHERE %objProductsTableFName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objProductName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return a field related to the given product URL;
     *
     * This method, given a product URL and a specific field to get, will retrieve that field from the database. It's a nice way,
     * knowing the URL of the product to get information about any defined field;
     *
     * @param S $objProductURL The product URL;
     * @param S $objFieldToGet The field to get;
     * @return mixed Depends on what was requested;
     */
    public function getProductInfoByURL (S $objProductURL, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objProductsTable)
        ->doToken ('%condition', new S ('WHERE %objProductsTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objProductURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given category id, while giving the corresponding field as a parameter to the function. An
     * easy way to get information about stored categories, without having too much of a problem with getting information about
     * them;
     *
     * @param S $objCategoryId The category id to query for;
     * @param S $objFieldToGet The category field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getCategoryInfoById (S $objCategoryId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFId = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given category name, while giving the corresponding field as a parameter to the function. An
     * easy way to get information about stored categories without having too much of a problem with getting information from them;
     *
     * @param S $objCategoryName The category name to query for;
     * @param S $objFieldToGet The category field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getCategoryInfoByName (S $objCategoryName, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFName = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will return information about a given category URL, while giving the corresponding field as a parameter to the function. An
     * easy way to get information about stored categories without having too much of a problem with getting information from them;
     *
     * @param S $objCategoryURL The category URL to query for;
     * @param S $objFieldToGet The category field to get information for;
     * @return mixed Really depends on what's asked to be returned;
     */
    public function getCategoryInfoByURL (S $objCategoryURL, S $objFieldToGet) {
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', self::$objCategoryTable)
        ->doToken ('%condition', new S ('WHERE %objCategoryTableFSEO = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCategoryURL))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will __CALL the proper methods;
     *
     * This method will call the proper methods, that aren't defined as self-standing methods, but are defined as cases in a
     * switch statement in this method. We do this because some of the functions here, due to our style of programming, share
     * many repetitive configuration options, meaning that for an action, we only write the code once, and if we need to
     * modify something somewhere, than we know exactly where to find it;
     *
     * Another argument is that for the moment no inheritance is really needed. Besides that, we could simulate inheritance by
     * doing a default CALL to the parent method, which means that if there wasn't any case that matched the current object scope,
     * than the calling function will be passed up the parent. It's a nice way of having extremely organized code, while keeping
     * the advantages of an almost full OOP programming style, without loss in performance;
     */
    public function __CALL ($objFunctionName, $objFunctionArgs) {
        switch ($objFunctionName) {
            default:
                return parent::__CALL ($objFunctionName, $objFunctionArgs);
                break;
        }
    }

        /**
     * Will render a requested widget;
     *
     * This method is used to render a widget that usually is used in the frontend part of any website done with the help of this
     * platform. What are widgets you ask?! Well, it's quite simple. They are pieces of PHP code, usually tied to some
     * configuration options that control the way the widget functions or showns;
     *
     * Usually, configured widgets have enough power to be used in any way you want or need. For most of the times, the widgets
     * are called in the proper section of the frontend, but this method must permit the use of widgets, independent of the place
     * the developer needs them;
     *
     * @param $objW The widget to render;
     * @return mixed Depends on the widget;
     */
    public function renderWidget (S $objW, A $objWA = NULL) {
        // Make an empty array if NULL ...
        if ($objWA == NULL) $objWA = new A;

        // XML & RSS: Do a switch ...
        switch ($objW) {
            case 'widgetXML':
                // BK;
                break;

            case 'widgetRSS':
                // BK;
                break;
        }

        // HTML: Do a switch ...
        switch ($objW) {
            case 'widgetCategoryList':
                // Set some requirements ...
                if ($objWA == NULL) $objWA = new A;

                // Get the category to start from ...
                if (isset ($objWA['start_from_category'])) {
                    // Get the category LIST;
                    $objCategoryList = $this->getCategories (NULL,
                    $objWA['start_from_category']);
                } else {
                    // Get the category LIST;
                    $objCategoryList = $this->getCategories (NULL, NULL);
                }

                // Set the template file ...
                $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                TPL::tpSet ($objCategoryList, new S ('objCategoryList'), $tpF);
                TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                TPL::tpSet ($this, new S ('ART'), $tpF);
                TPL::tpExe ($tpF);
                break;

            case 'widgetList':
                // Check some needed requirements ...
                if ($_GET[FRONTEND_SECTION_URL] == FRONTEND_PRODUCTS_URL) {
                    // Set some requirements ...
                    $objPag = isset ($_GET[PRODUCTS_PAGE_URL]) ? $_GET[PRODUCTS_PAGE_URL]: new S ((string) 1);

                    if (isset ($_GET[PRODUCTS_ITEM_URL])) {
                        // Check that the article exists, before doing anything stupid ...
                        if ($this->checkProductURLIsUnique ($objURL =
                        $_GET[PRODUCTS_ITEM_URL])->toBoolean () == TRUE) {
                            // Make the proper header, at first ...
                            $this->setHeaderStr (new S (HDR::HEADER_404_NOT_FOUND));

                            // Give me back my free hardcore, Quoth the server, '404' ...
                            $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                            new A (Array ('404'))), new S ('Location'));
                        } else {
                            // Do me SEO, yah baby! ...
                            TPL::manageTTL ($this->getProductInfoByURL ($objURL, self::$objProductsTableFName));
                            TPL::manageTAG (new S ('description'), $this->getProductInfoByURL ($objURL,
                            self::$objProductsTableFDescription)->entityDecode (ENT_QUOTES)->stripTags ()->doToken (_QOT, _NONE));

                            // Set some requirements ...
                            $objPathToItem = self::$objMPTT->mpttGetSinglePath ($this->getCategoryInfoById ($this
                            ->getProductInfoByURL ($objURL, self::$objProductsTableFCategoryId), self::$objCategoryTableFName));
                            $objItemPpties = $this->getPropertiesByProductURL ($objURL);
                            $objItemImages = $this->getImagesByProductURL ($objURL);
                            if ($objItemImages->doCount ()->toInt () != 0) {
                                $objItemHasImg = new B (TRUE);
                            } else {
                                $objItemHasImg = new B (FALSE);
                            }

                            // Set the template file ...
                            $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . '-Item.tp');
                            TPL::tpSet ($objPathToItem, new S ('objPathToItem'), $tpF);
                            TPL::tpSet ($objItemPpties, new S ('objItemPpties'), $tpF);
                            TPL::tpSet ($objItemImages, new S ('objItemImages'), $tpF);
                            TPL::tpSet ($objItemHasImg, new S ('objItemHasImg'), $tpF);
                            TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                            TPL::tpSet ($objURL, new S ('objURL'), $tpF);
                            TPL::tpSet ($this->ATH, new S ('ATH'), $tpF);
                            TPL::tpSet ($this->STG, new S ('STG'), $tpF);
                            TPL::tpSet ($this, new S ('PRD'), $tpF);
                            TPL::tpExe ($tpF);
                        }
                    } else {
                        if (isset ($_GET[PRODUCTS_CATEGORY_URL])) {
                            // Check that the category exists, before doing anything stupid ...
                            if ($this->checkCategoryURLIsUnique ($objCat =
                            $_GET[PRODUCTS_CATEGORY_URL])->toBoolean () == TRUE) {
                                // Make the proper header, at first ...
                                $this->setHeaderStr (new S (HDR::HEADER_404_NOT_FOUND));

                                // Give me back my free hardcore, Quoth the server, '404' ...
                                $this->setHeaderKey (URL::staticURL (new A (Array (FRONTEND_ERROR_URL)),
                                new A (Array ('404'))), new S ('Location'));
                            } else {
                                // Do me SEO, yah baby! ...
								err ('Here');
                            }
                        } else {
                            // Do me SEO, yah baby! ...
                            TPL::manageTTL (_S (FRONTEND_PRODUCTS_URL));
                            TPL::manageTTL (_S (PRODUCTS_PAGE_URL)->appendString (_SP)->appendString ($objPag));

                            // Set some requirements ...
                            $objCnt = $this->getProductCount ();
                            $objArt = $this->getProductsByPage ($objPag);
                        }

                        // Set the template file ...
                        $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . $objW . TPL_EXTENSION);
                        TPL::tpSet ($objWA, new S ('objWidgetArray'), $tpF);
                        TPL::tpSet ($objArt, new S ('objAr'), $tpF);
                        TPL::tpSet ($this, new S ('PRD'), $tpF);
                        TPL::tpExe ($tpF);

                        // Set them paginations ...
                        if ($objCnt->toInt () > (int) self::$objItemsPerPage->toString ())
                        self::$objFrontend->setPagination ($objCnt, new I ((int) self::$objItemsPerPage->toString ()));
                    }
                } else {
                    // Do the biggest error on the PLANET ...
                    self::renderScreenOfDeath (new S (__CLASS__),
                    new S (ARTICLES_NEED_PROPER_SECTION),
                    new S (ARTICLES_NEED_PROPER_SECTION_FIX));
                }
                // BK;
                break;
        }
    }

    /**
     * Will render a specified form, the name of the form given by the first parameter;
     *
     * This method will render one of the forms for our object, invoked by giving the proper form identifier to the current form.
     * We have chosen this method of invoking forms, because we just had too many this->renderSomethingMethod (), which really had
     * an impact on code massiveness. Also, having code organized in switch/case statements leads us to be able to share common
     * settings between different forms, as we've done with the methods defined in the __CALL method above;
     *
     * For example, if we wanted to share some common configuration between a create and an edit form, we could have introduced
     * two switches in this method, one that would have set the common options, and the second, would have just passed through
     * again, and get the already set configuration options, using them. This means that if we needed to change behavior of
     * some interconnected forms, that would mean modifying the needed code one place only, which is a big advantage over
     * having separated methods for each form. Maybe if we extended this object, you guys could understand the functionality;
     *
     * @param string $objFormToRender The name of the form to render;
     * @return mixed Depends on the rendered form if it returns something or not;
     */
    public function renderBackendPage (S $objPageToRender) {
        // Get a specific CSS file for this controller ...
        TPL::manageCSS (new FilePath ($this->getPathToSkinCSS ()
        ->toRelativePath () . $objPageToRender . CSS_EXTENSION), $objPageToRender);

        // Do pagination ...
        if (isset ($_GET[ADMIN_PAGINATION])) {
            $objLowerLimit = (int) $_GET[ADMIN_PAGINATION]->toString () * 10 - 10;
            $objUpperLimit = 10;
        } else {
            $objLowerLimit = 0;
            $objUpperLimit = 10;
        }

        // Do a switch on the rendered page ...
        switch ($objPageToRender) {
            case 'manageImages':
                // Make an IF;
                if (isset ($_GET[PRODUCTS_ACTION_IMAGE])) {
                    // Do a switch ...
                    switch ($_GET[PRODUCTS_ACTION_IMAGE]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('imageEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('imageErase'));
                            break;
                    }
                } else {
                    // Set some requirements ...
                    $objGetCondition = new S;
                    // Do a sorting beforehand;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByImage':
                            case 'DescByImage':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objProductsIMGTableFURL');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByImage':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByImage':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByTitle':
                            case 'DescByTitle':
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objProductsIMGTableFTitle');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByTitle':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByTitle':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;
                        }
                    } else {
                        // Set some requirements ...
                        $objGetCondition->appendString (_SP)
                        ->appendString ('ORDER BY %objProductsIMGTableFId DESC');
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objImageTable = $this->getImagesByProductId ($_GET[ADMIN_ACTION_ID], $objGetCondition);
                    $objImageTableCount = $this->getImageCountByProductId ($_GET[ADMIN_ACTION_ID]);

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageImages.tp');
                    TPL::tpSet ($objImageTable, new S ('imageTable'), $tpF);
                    TPL::tpSet ($this, new S ('thisObj'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination;
                    if ($objImageTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objImageTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('imageCreate'));
                }
                // Break out ...
                break;

            case 'manageProperties':
                if (isset ($_GET[PRODUCTS_ACTION_PROPERTY])) {
                    // Do a switch ...
                    switch ($_GET[PRODUCTS_ACTION_PROPERTY]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('propertyEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('propertyErase'));
                            break;
                    }
                } else {
                    // Set some requirements ...
                    $objGetCondition = new S;
                    // Do a sorting beforehand;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByKey':
                            case 'DescByKey':
                                // Set some requiremenst;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objProductsPropertyTableFKey');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByKey':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByKey':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByVar':
                            case 'DescByVar':
                                // Set some requiremenst;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objProductsPropertyTableFVar');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByVar':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByVar':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;
                        }
                    } else {
                        // Set some requirements ...
                        $objGetCondition->appendString (_SP)
                        ->appendString ('ORDER BY %objProductsPropertyTableFId DESC');
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objPropertyTable = $this->getPropertiesByProductId ($_GET[ADMIN_ACTION_ID], $objGetCondition);
                    $objPropertyTableCount = $this->getPropertyCountByProductId ($_GET[ADMIN_ACTION_ID]);

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageProperties.tp');
                    TPL::tpSet ($objPropertyTable, new S ('propertyTable'), $tpF);
                    TPL::tpSet ($this, new S ('thisObj'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination;
                    if ($objPropertyTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objPropertyTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('propertyCreate'));
                }
                // Break out ...
                break;

            case 'manageProducts':
                // Set some requirements;
                TPL::manageJSS (new FilePath ($this->getPathToSkinJSS ()
                ->toRelativePath () . 'manageProducts.js'), new S ('manageProducts'));

                // Do some work;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch;
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('productEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('productErase'));
                            break;

                        case ADMIN_ACTION_IMAGES:
                            // Specific ...
                            $this->renderBackendPage (new S ('manageImages'));
                            break;

                        case ADMIN_ACTION_PROPERTIES:
                            // Specific ...
                            $this->renderBackendPage (new S ('manageProperties'));
                            break;
                    }
                } else {
                    // Set some requirements ...
                    $objGetCondition = new S;
                    // Do a sorting beforehand;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByCode':
                            case 'DescByCode':
                                // Set some requirements;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objProductsTableFCode');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByCode':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByCode':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByName':
                            case 'DescByName':
                                // Set some requirements;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objProductsTableFName');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByName':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByName':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByStoc':
                            case 'DescByStoc':
                                // Set somre requirements;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objProductsTableFStoc');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByStoc':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByStoc':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByPrice':
                            case 'DescByPrice':
                                // Set some requirements;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ORDER BY %objProductsTableFPrice');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByPrice':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByPrice':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;

                            case 'AscByCategory':
                            case 'DescByCategory':
                                // Set some requirements;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('INNER JOIN %objProductsCategoryTable AS t2
                                ON t1.%objProductsTableFCategoryId = t2.%objCategoryTableFId
                                ORDER BY t2.%objCategoryTableFName');
                                // Do a switch ...
                                switch ($_GET[ADMIN_ACTION_SORT]) {
                                    case 'AscByCategory':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('ASC');
                                        break;

                                    case 'DescByCategory':
                                        $objGetCondition->appendString (_SP)
                                        ->appendString ('DESC');
                                        break;
                                }
                                // Break out ...
                                break;
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objProductTable = $this->getProducts ($objGetCondition);
                    $objProductTableCount = $this->getProductCount ();

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageProducts.tp');
                    TPL::tpSet ($objProductTable, new S ('productTable'), $tpF);
                    TPL::tpSet ($this, new S ('thisObj'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do some pagination;
                    if ($objProductTableCount->toInt () > 10)
                    self::$objAdministration->setPagination ($objPropertyTableCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('productCreate'));
                }
                // Break out ...
                break;

            case 'manageCategories':
                // Add some requirements;
                TPL::manageJSS (new FilePath ($this->getPathToSkinJSS ()
                ->toRelativePath () . 'manageCategories.js'), new S ('manageCategories'));

                // Check if there's an action to take;
                if (isset ($_GET[ADMIN_ACTION])) {
                    // Do a switch ...
                    switch ($_GET[ADMIN_ACTION]) {
                        case ADMIN_ACTION_EDIT:
                            $this->renderForm (new S ('categoryEdit'));
                            break;

                        case ADMIN_ACTION_ERASE:
                            $this->renderForm (new S ('categoryErase'));
                            break;

                        case ADMIN_ACTION_MOVE:
                            $this->renderForm (new S ('categoryMove'));
                            break;
                    }
                } else {
                    // Set some requirements ...
                    $objGetCondition = new S;
                    // Do a sorting, before anything else;
                    if (isset ($_GET[ADMIN_ACTION_SORT])) {
                        // Do a switch ...
                        switch ($_GET[ADMIN_ACTION_SORT]) {
                            case 'AscByCategory':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('ASC');
                                break;

                            case 'DescByCategory':
                                // Make the ordered condition;
                                $objGetCondition->appendString (_SP)
                                ->appendString ('DESC');
                        }
                    }

                    // Add some LIMITs
                    $objGetCondition->appendString (_SP)->appendString ('LIMIT %LowerLimit, %UpperLimit')
                    ->doToken ('%LowerLimit', $objLowerLimit)->doToken ('%UpperLimit', $objUpperLimit);

                    // Set some requirements ...
                    $objCategoryTreeCount = $this->getCategoryCount ();
                    $objCategoryTree = $this->getCategories (isset ($_GET[ADMIN_SHOW_ALL]) ? new S : $objGetCondition);

                    // Set the template file ...
                    $tpF = new FilePath ($this->getPathToSkin ()->toRelativePath () . 'manageCategories.tp');
                    TPL::tpSet ($objCategoryTree, new S ('categoryTree'), $tpF);
                    TPL::tpExe ($tpF);

                    // Do pagination ...
                    if ($objCategoryTreeCount->toInt () > 10 && !isset ($_GET[ADMIN_SHOW_ALL]))
                    self::$objAdministration->setPagination ($objCategoryTreeCount);

                    // Do the form, make it happen;
                    $this->renderForm (new S ('categoryCreate'));
                }
                // Break out ...
                break;

            case 'manageOperations':
                // Do the form, make it happen;
            	$this->renderForm (new S ('categoryMoveOperation'));
                break;

            case 'manageConfiguration':
                // Do the form, make it happen ...
                if (isset ($_GET[ADMIN_ACTION])) { $this->renderForm ($_GET[ADMIN_ACTION]); }
                else { $this->renderForm (new S ('configurationEdit')); }
                break;
        }
    }

    /**
     * Will render a specified form, the name of the form given by the first parameter;
     *
     * This method will render one of the forms for our object, invoked by giving the proper form identifier to the current form.
     * We have chosen this method of invoking forms, because we just had too many this->renderSomethingMethod (), which really had
     * an impact on code massiveness. Also, having code organized in switch/case statements leads us to be able to share common
     * settings between different forms, as we've done with the methods defined in the __CALL method above;
     *
     * For example, if we wanted to share some common configuration between a create and an edit form, we could have introduced
     * two switches in this method, one that would have set the common options, and the second, would have just passed through
     * again, and get the already set configuration options, using them. This means that if we needed to change behavior of
     * some interconnected forms, that would mean modifying the needed code one place only, which is a big advantage over
     * having separated methods for each form. Maybe if we extended this object, you guys could understand the functionality;
     *
     * @param string $objFormToRender The name of the form to render;
     * @return mixed Depends on the rendered form if it returns something or not;
     */
    public function renderForm (S $objFormToRender, A $objFormArray = NULL) {
        // Make them defaults ...
        if ($objFormArray == NULL) $objFormArray = new A;

        // Do a switch ...
        switch ($objFormToRender) {
            case 'propertyCreate':
                // Set the URL to go back;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    if ($this->checkPOST (self::$objProductsPropertyTableFKey)->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objProductsPropertyTableFKey,
                        new S (PRODUCTS_PROPERTY_CANNOT_BE_EMPTY));
                    }

                    if ($this->checkPOST (self::$objProductsPropertyTableFVar)->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objProductsPropertyTableFVar,
                        new S (PRODUCTS_VALUE_CANNOT_BE_EMPTY));
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (PRODUCTS_ADD_PROPERTY))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objProductsPropertyTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objProductsPropertyTableFId)
                ->setExtraUpdateData (self::$objProductsPropertyTableFPId, $_GET[ADMIN_ACTION_ID])
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S (PRODUCTS_ADD_PROPERTY))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsPropertyTableFKey)
                ->setLabel (new S (PRODUCTS_PROPERTY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objProductsPropertyTableFVar)
                ->setLabel (new S (PRODUCTS_VALUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'propertyEdit':
                // Set the URL to go back;
                $objURLToGoBack = URL::rewriteURL (new A (Array (PRODUCTS_ACTION_PROPERTY, PRODUCTS_ID_PROPERTY)));

                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    if ($this->checkPOST (self::$objProductsPropertyTableFKey)->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objProductsPropertyTableFKey,
                        new S (PRODUCTS_PROPERTY_CANNOT_BE_EMPTY));
                    }

                    if ($this->checkPOST (self::$objProductsPropertyTableFVar)->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objProductsPropertyTableFVar,
                        new S (PRODUCTS_VALUE_CANNOT_BE_EMPTY));
                    }
                }

                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (PRODUCTS_EDIT_PROPERTY))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objProductsPropertyTable)
                ->setUpdateId ($_GET[PRODUCTS_ID_PROPERTY])
                ->setUpdateField (self::$objProductsPropertyTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setValue (new S (PRODUCTS_EDIT_PROPERTY))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsPropertyTableFKey)
                ->setLabel (new S (PRODUCTS_PROPERTY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objProductsPropertyTableFVar)
                ->setLabel (new S (PRODUCTS_VALUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'propertyErase':
                // Set the URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (PRODUCTS_ACTION_PROPERTY, PRODUCTS_ID_PROPERTY)));

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objProductsPropertyTable)
                ->doToken ('%condition', new S ('%objProductsPropertyTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[PRODUCTS_ID_PROPERTY]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'imageCreate':
                // Set the URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST (self::$objProductsIMGTableFTitle)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsIMGTableFTitle)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsIMGTableFTitle,
                        new S (PRODUCTS_IMAGE_TITLE_CANNOT_BE_EMPTY));
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (PRODUCTS_ADD_IMAGE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objProductsIMGTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objProductsIMGTableFId)
                ->setUploadDirectory (new S ('products/images/' . $_GET[ADMIN_ACTION_ID]))
                ->setUploadImageResize (new A (Array (60 => 60, 100 => 100, 128 => 128, 320 => 240, 640 => 480, 800 => 600)))
                ->setExtraUpdateData (self::$objProductsIMGTableFProdId, $_GET[ADMIN_ACTION_ID])
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setValue (new S (PRODUCTS_ADD_IMAGE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsIMGTableFTitle)
                ->setLabel (new S (PRODUCTS_IMAGE_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (self::$objProductsIMGTableFURL)
                ->setLabel (new S (PRODUCTS_IMAGE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objProductsIMGTableFCaption)
                ->setLabel (new S (PRODUCTS_CAPTION))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'imageEdit':
                // Set the URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (PRODUCTS_ACTION_IMAGE, PRODUCTS_ID_IMAGE)));

                // Do the form, make it happen;
                if ($this->checkPOST (self::$objProductsIMGTableFTitle)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsIMGTableFTitle)
                    ->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsIMGTableFTitle,
                        new S (PRODUCTS_IMAGE_TITLE_CANNOT_BE_EMPTY));
                    }
                }

                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (PRODUCTS_EDIT_IMAGE))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objProductsIMGTable)
                ->setUpdateId ($_GET[PRODUCTS_ID_IMAGE])
                ->setUpdateField (self::$objProductsIMGTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setUploadDirectory (new S ('products/images/' . $_GET[ADMIN_ACTION_ID]))
                ->setUploadImageResize (new A (Array (60 => 60, 100 => 100, 128 => 128, 320 => 240, 640 => 480, 800 => 600)))
                ->setInputType (new S ('submit'))
                ->setValue (new S (PRODUCTS_EDIT_IMAGE))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsIMGTableFTitle)
                ->setLabel (new S (PRODUCTS_IMAGE_TITLE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (self::$objProductsIMGTableFURL)
                ->setLabel (new S (PRODUCTS_IMAGE))
                ->setFileController (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objProductsIMGTableFCaption)
                ->setLabel (new S (PRODUCTS_CAPTION))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'imageErase':
                // Set the URL to back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (PRODUCTS_ACTION_IMAGE, PRODUCTS_ID_IMAGE)));

                // First, delete existing images;
                $objProductImages = $this->_Q (_QS ('doSELECT')
                ->doToken ('%what', self::$objProductsIMGTableFURL)
                ->doToken ('%table', self::$objProductsIMGTable)
                ->doToken ('%condition', new S ('WHERE %objProductsIMGTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[PRODUCTS_ID_IMAGE]));

                // Knowing the images, delete THEM;
                foreach ($objProductImages as $k => $v) {
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images/' .
                    $_GET[ADMIN_ACTION_ID] . _S . $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images/' .
                    $_GET[ADMIN_ACTION_ID] . _S . '60_60_' . $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images/' .
                    $_GET[ADMIN_ACTION_ID] . _S . '100_100_' .  $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images/' .
                    $_GET[ADMIN_ACTION_ID] . _S . '128_128_' .  $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images/' .
                    $_GET[ADMIN_ACTION_ID] . _S . '320_240_' .  $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images/' .
                    $_GET[ADMIN_ACTION_ID] . _S . '640_480_' .  $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images/' .
                    $_GET[ADMIN_ACTION_ID] . _S . '800_600_' .  $v['url']);
                }

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objProductsIMGTable)
                ->doToken ('%condition', new S ('%objProductsIMGTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[PRODUCTS_ID_IMAGE]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'productCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work;
                if ($this->checkPOST (self::$objProductsTableFCode)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsTableFCode)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsTableFCode,
                        new S (PRODUCTS_CODE_MUST_BE_ENTERED));
                    } else {
                        if ($this->checkCodeIsUnique ($this
                        ->getPOST (self::$objProductsTableFCode))->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objProductsTableFCode,
                            new S (PRODUCTS_CODE_MUST_BE_UNIQUE));
                        }
                    }
                }

                if ($this->checkPOST (self::$objProductsTableFName)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsTableFName)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsTableFName,
                        new S (PRODUCTS_NAME_MUST_BE_ENTERED));
                    } else {
                        if ($this->checkProductNameIsUnique ($this
                        ->getPOST (self::$objProductsTableFName))->toBoolean () == FALSE) {
                            $this->setErrorOnInput (self::$objProductsTableFName,
                            new S (PRODUCTS_NAME_MUST_BE_UNIQUE));
                        }
                    }
                }

                if ($this->checkPOST (self::$objProductsTableFName)->toBoolean () == TRUE) {
                    if ($this->checkProductURLIsUnique (URL::getURLFromString ($this
                    ->getPOST (self::$objProductsTableFName)))->toBoolean () == FALSE) {
                        $this->setErrorOnInput (self::$objProductsTableFName,
                        new S (PRODUCTS_URL_MUST_BE_UNIQUE));
                    }
                }

                if ($this->checkPOST (self::$objProductsTableFStoc)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsTableFStoc)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsTableFStoc,
                        new S (PRODUCTS_STOCK_MUST_BE_ENTERED));
                    } else {
                        // Get some requirements;
                        $objStock = new I ((int) $this->getPOST (self::$objProductsTableFStoc)->toString ());

                        // Do some MORE validation;
                        if ($objStock->toInt () < 0) {
                            $this->setErrorOnInput (self::$objProductsTableFStoc,
                            new S (PRODUCTS_STOCK_CANNOT_BE_NEGATIVE));
                        }
                    }
                }

                if ($this->checkPOST (self::$objProductsTableFPrice)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsTableFPrice)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsTableFPrice,
                        new S (PRODUCTS_PRICE_MUST_BE_ENTERED));
                    } else {
                        // Get some requirements;
                        $objPrice = new I ((int) $this->getPOST (self::$objProductsTableFPrice)->toString ());

                        // Do some MORE validation;
                        if ($objPrice->toInt () < 0) {
                            $this->setErrorOnInput (self::$objProductsTableFPrice,
                            new S (PRODUCTS_PRICE_CANNOT_BE_NEGATIVE));
                        }
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (PRODUCTS_ADD_PRODUCT))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objProductsTable)
                ->setUpdateId (new S ('#nextTableAutoIncrement'))
                ->setUpdateField (self::$objProductsTableFId)
                ->setUploadDirectory (new S ('products/pdf'))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE));

                // Set some EXTRA data;
                if ($this->checkPOST (self::$objProductsTableFName)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objProductsTableFSEO,
                    URL::getURLFromString ($this->getPOST (self::$objProductsTableFName)));
                }

                // Continue;
                $this->setInputType (new S ('submit'))
                ->setValue (new S (PRODUCTS_ADD_PRODUCT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objProductsTableFCategoryId)
                ->setLabel (new S (PRODUCTS_CATEGORY));

                // Category parent or brother of this one ...
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))->setName ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique ($v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Continue;
                $this->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFCode)
                ->setLabel (new S (PRODUCTS_CODE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFName)
                ->setLabel (new S (PRODUCTS_NAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFURL)
                ->setLabel (new S (PRODUCTS_URL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFStoc)
                ->setLabel (new S (PRODUCTS_STOCK))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFPrice)
                ->setLabel (new S (PRODUCTS_PRICE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (self::$objProductsTableFPDF)
                ->setLabel (new S (PRODUCTS_PDF))
                ->setFileController (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objProductsTableFDescription)
                ->setLabel (new S (PRODUCTS_DECRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'productEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // Do some work;
                if ($this->checkPOST (self::$objProductsTableFCode)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsTableFCode)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsTableFCode,
                        new S (PRODUCTS_CODE_MUST_BE_ENTERED));
                    } else {
                        if ($this->getPOST (self::$objProductsTableFCode) != $this
                        ->getProductInfoById ($_GET[ADMIN_ACTION_ID], self::$objProductsTableFCode)) {
                            if ($this->checkCodeIsUnique ($this
                            ->getPOST (self::$objProductsTableFCode))->toBoolean () == FALSE) {
                                $this->setErrorOnInput (self::$objProductsTableFCode,
                                new S (PRODUCTS_CODE_MUST_BE_UNIQUE));
                            }
                        }
                    }
                }

                if ($this->checkPOST (self::$objProductsTableFName)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsTableFName)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsTableFName,
                        new S (PRODUCTS_NAME_MUST_BE_ENTERED));
                    } else {
                        if ($this->getPOST (self::$objProductsTableFName) != $this
                        ->getProductInfoById ($_GET[ADMIN_ACTION_ID], self::$objProductsTableFName)) {
                            if ($this->checkProductNameIsUnique ($this
                            ->getPOST (self::$objProductsTableFName))->toBoolean () == FALSE) {
                                $this->setErrorOnInput (self::$objProductsTableFName,
                                new S (PRODUCTS_NAME_MUST_BE_UNIQUE));
                            }

                            if ($this->checkPOST (self::$objProductsTableFName)->toBoolean () == TRUE) {
                                if ($this->checkProductURLIsUnique (URL::getURLFromString ($this
                                ->getPOST (self::$objProductsTableFName)))->toBoolean () == FALSE) {
                                    $this->setErrorOnInput (self::$objProductsTableFName,
                                    new S (PRODUCTS_URL_MUST_BE_UNIQUE));
                                }
                            }
                        }
                    }
                }

                if ($this->checkPOST (self::$objProductsTableFStoc)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsTableFStoc)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsTableFStoc,
                        new S (PRODUCTS_STOCK_MUST_BE_ENTERED));
                    } else {
                        // Get some requirements;
                        $objStock = new I ((int) $this->getPOST (self::$objProductsTableFStoc)->toString ());

                        // Do some MORE validation;
                        if ($objStock->toInt () < 0) {
                            $this->setErrorOnInput (self::$objProductsTableFStoc,
                            new S (PRODUCTS_STOCK_CANNOT_BE_NEGATIVE));
                        }
                    }
                }

                if ($this->checkPOST (self::$objProductsTableFPrice)->toBoolean () == TRUE) {
                    if ($this->getPOST (self::$objProductsTableFPrice)->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (self::$objProductsTableFPrice,
                        new S (PRODUCTS_PRICE_MUST_BE_ENTERED));
                    } else {
                        // Get some requirements;
                        $objPrice = new I ((int) $this->getPOST (self::$objProductsTableFPrice)->toString ());

                        // Do some MORE validation;
                        if ($objPrice->toInt () < 0) {
                            $this->setErrorOnInput (self::$objProductsTableFPrice,
                            new S (PRODUCTS_PRICE_CANNOT_BE_NEGATIVE));
                        }
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setEnctype (new S ('multipart/form-data'))
                ->setFieldset (new S (PRODUCTS_EDIT_PRODUCT))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objProductsTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objProductsTableFId)
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setAJAXEnabledForm (new B (FALSE));

                // Set some EXTRA data;
                if ($this->checkPOST (self::$objProductsTableFName)->toBoolean () == TRUE) {
                    $this->setExtraUpdateData (self::$objProductsTableFSEO,
                    URL::getURLFromString ($this->getPOST (self::$objProductsTableFName)));
                }

                // Continue;
                $this->setInputType (new S ('submit'))
                ->setValue (new S (PRODUCTS_EDIT_PRODUCT))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (self::$objProductsTableFCategoryId)
                ->setLabel (new S (PRODUCTS_CATEGORY));

                // Category parent or brother of this one ...
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))->setName ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique ($v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Continue;
                $this->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFCode)
                ->setLabel (new S (PRODUCTS_CODE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFName)
                ->setLabel (new S (PRODUCTS_NAME))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFURL)
                ->setLabel (new S (PRODUCTS_URL))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFStoc)
                ->setLabel (new S (PRODUCTS_STOCK))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setName (self::$objProductsTableFPrice)
                ->setLabel (new S (PRODUCTS_PRICE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('file'))
                ->setName (self::$objProductsTableFPDF)
                ->setLabel (new S (PRODUCTS_PDF))
                ->setFileController (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objProductsTableFDescription)
                ->setLabel (new S (PRODUCTS_DECRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'productErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));

                // First, delete existing images;
                $objProductImages = $this->_Q (_QS ('doSELECT')
                ->doToken ('%what', self::$objProductsIMGTableFURL)->doToken ('%table', self::$objProductsIMGTable)
                ->doToken ('%condition', new S ('WHERE %objProductsIMGTableFProdId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Knowing the images, delete THEM;
                foreach ($objProductImages as $k => $v) {
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images' . _S . $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images' . _S . '60_60_' . $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images' . _S . '100_100_' .  $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images' . _S . '128_128_' .  $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images' . _S . '320_240_' .  $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images' . _S . '640_480_' .  $v['url']);
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/images' . _S . '800_600_' .  $v['url']);
                }

                // Do erase associated images;
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objProductsIMGTable)
                ->doToken ('%condition', new S ('%objProductsIMGTableFProdId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do erase associated properties ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objProductsPropertyTable)
                ->doToken ('%condition', new S ('%objProductsPropertyTableFPId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do erase associated PDF file;
                $objPDF = $this->getProductInfoById ($_GET[ADMIN_ACTION_ID], self::$objProductsTableFPDF);
                if ($objPDF->toLength ()->toInt () != 0) {
                    UNLINK (DOCUMENT_ROOT . UPLOAD_DIR . _S . 'products/pdf' . _S . $objPDF);
                }

                // Do erase it ...
                $this->_Q (_QS ('doDELETE')
                ->doToken ('%table', self::$objProductsTable)
                ->doToken ('%condition', new S ('%objProductsTableFId = "%Id"'))
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]));

                // Do a redirect, and get the user back where he belongs;
                $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                break;

            case 'categoryCreate':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL ();

                // Do some work ...
                if ($this->checkPOST (new S ('categories_show_all'))->toBoolean () == TRUE) {
                    // Redirect to proper ...
                    $this->setHeaderKey (URL::rewriteURL (new A (Array (ADMIN_SHOW_ALL)),
                    new A (Array ('1'))), new S ('Location'));
                }

                if ($this->checkPOST (new S ('add_category_submit'))->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    $objToCheck = MPTT::mpttAddUnique ($this->getPOST (new S ('add_category')),
                    new S ((string) $_SERVER['REQUEST_TIME']));

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        $this->setErrorOnInput (new S ('add_category'),
                        new S (CATEGORY_NAME_CANNOT_BE_EMPTY));

                        // Set the memory;
                        $objFormHappened->switchType ();
                    } else {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($objToCheck)
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists, and tell the user the group exists;
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (CATEGORY_ALREADY_EXISTS));

                            // Set the memory;
                            $objFormHappened->switchType ();
                        }

                        if ($this->checkCategoryURLIsUnique (URL::getURLFromString ($objToCheck))
                        ->toBoolean () == FALSE) {
                            $this->setErrorOnInput (new S ('add_category'),
                            new S (PRODUCTS_CATEGORY_URL_MUST_BE_UNIQUE));
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();

                    if ($objFormHappened->toBoolean () == FALSE) {
                        // Remember if we should add it as a brother or child;
                        $objAddNodeAS = NULL;
                        switch ($this->getPOST (new S ('add_category_as_what'))) {
                            case PRODUCTS_CATEGORY_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::FIRST_CHILD);
                                break;

                            case PRODUCTS_CATEGORY_LAST_CHILD:
                                $objAddNodeAS = new S ((string)
                                MPTT::LAST_CHILD);
                                break;

                            case PRODUCTS_CATEGORY_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::PREVIOUS_BROTHER);
                                break;

                            case PRODUCTS_CATEGORY_NEXT_BROTHER:
                                $objAddNodeAS = new S ((string)
                                MPTT::NEXT_BROTHER);
                                break;
                        }
                        // Add the node;
                        self::$objMPTT->mpttAddNode ($objToCheck,
                        $this->getPOST (new S ('add_category_parent_or_bro')), $objAddNodeAS);
                    }
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (PRODUCTS_ADD_CATEGORY))
                ->setName ($objFormToRender);
                if ($this->checkPOST (new S ('add_category_submit'))->toBoolean () == TRUE)
                $this->setRedirect ($objURLToGoBack);
                $this->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('categories_show_all'))
                ->setValue (new S (PRODUCTS_SHOW_ALL_CATEGORIES))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('submit'))
                ->setName (new S ('add_category_submit'))
                ->setValue (new S (PRODUCTS_ADD_CATEGORY))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('text'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category'))
                ->setLabel (new S (PRODUCTS_CATEGORY_NAME_LABEL))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_as_what'))
                ->setLabel (new S (PRODUCTS_AS_A))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_first_child'))
                ->setLabel (new S (PRODUCTS_CATEGORY_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_last_child'))
                ->setLabel (new S (PRODUCTS_CATEGORY_LAST_CHILD))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_previous_brother'))
                ->setLabel (new S (PRODUCTS_CATEGORY_BROTHER))
                ->setInputType (new S ('option'))
                ->setName (new S ('as_next_brother'))
                ->setLabel (new S (PRODUCTS_CATEGORY_NEXT_BROTHER))
                ->setInputType (new S ('select'))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('add_category_parent_or_bro'))
                ->setLabel (new S (PRODUCTS_OF_CATEGORY));

                // Category parent or brother of this one ...
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName ($v[self::$objMPTT->objNameOfNode])
                    ->setValue ($v[self::$objMPTT->objNameOfNode])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique (CLONE $v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Continue, execute the form ...
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'categoryEdit':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));
				$objFormHappened = new B (FALSE);

                // Do validation and error on it if something goes wrong;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    // Set some requirements;
                    $objFormHappened = new B (FALSE);
                    $objToCheck = MPTT::mpttAddUnique ($this->getPOST (self::$objCategoryTableFName), $this
                    ->getCategoryInfoById ($_GET[ADMIN_ACTION_ID], self::$objCategoryTableFDate));

                    if ($objToCheck->toLength ()->toInt () == 0) {
                        // Check for empty group name, and error on it. We don't allow empty group names;
                        $this->setErrorOnInput (self::$objCategoryTableFName,
                        new S (PRODUCTS_CATEGORY_NAME_EMPTY));

                        // Set the memory;
                        $objFormHappened->switchType ();
                    } else if ($this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                    self::$objCategoryTableFName) != $objToCheck) {
                        if (self::$objMPTT->mpttCheckIfNodeExists ($objToCheck)
                        ->toBoolean () == TRUE) {
                            // Check to see if the group exists;
                            $this->setErrorOnInput (self::$objCategoryTableFName,
                            new S (PRODUCTS_CATEGORY_ALREADY_EXISTS));

                            // Set the memory;
                            $objFormHappened->switchType ();
                        }
                    }

                    // Get AJAX;
                    $this->getAjaxErrors ();
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objCategoryTable)
                ->setUpdateId ($_GET[ADMIN_ACTION_ID])
                ->setUpdateField (self::$objCategoryTableFId)
                ->setFieldset (new S (PRODUCTS_EDIT_CATEGORY));
				if ($this->checkPOST (self::$objCategoryTableFName)->toBoolean () == TRUE)
				$this->setExtraUpdateData (self::$objCategoryTableFSEO,
				URL::getURLFromString ($this->getPOST (self::$objCategoryTableFName)));
				if ($objFormHappened->toBoolean () == FALSE &&
				$this->checkPOST ()->toBoolean () == TRUE)
				$this->setRedirect ($objURLToGoBack);
                $this->setName ($objFormToRender)
                ->setAJAXEnabledForm (new B (FALSE))
                ->setInputType (new S ('submit'))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setName (new S ('edit_category_submit'))
                ->setValue (new S (PRODUCTS_EDIT_CATEGORY))
                ->setInputType (new S ('text'))
                ->setName (self::$objCategoryTableFName)
                ->setMPTTRemoveUnique (new B (TRUE))
                ->setLabel (new S (PRODUCTS_NAME))
                ->setJSRegExpReplace (new S ('[^a-zA-Z0-9 -]'))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('textarea'))
                ->setName (self::$objCategoryTableFDescription)
                ->setLabel (new S (PRODUCTS_DECRIPTION))
                ->setTinyMCETextarea (new B (TRUE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;

            case 'categoryErase':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_ID)));
                // Memorize if it has kids ...
                $objNodeHasKids = new B (FALSE);

                // Check if the category has articles;
                if ($this->getProductCount (_S ('WHERE %objProductsTableFCategoryId = "%Id"')
                ->doToken ('%Id', $_GET[ADMIN_ACTION_ID]))->toInt () != 0) {
                    // Set an error message;
                    self::$objAdministration->setErrorMessage (new S
                    (PRODUCTS_CANNOT_DELETE_CATEGORY), $objURLToGoBack);
                } else {
                    // Do erase the group node from the table;
                    self::$objMPTT->mpttRemoveNode ($this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID],
                    self::$objCategoryTableFName));

                    // Do a redirect, and get the user back where he belongs;
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                // Break out ...
                break;

            case 'categoryMove':
                // Set some predefines;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION, ADMIN_ACTION_TO, ADMIN_ACTION_TYPE)));

                // Get names, as they are unique;
                $objThatIsMoved = $this->getCategoryInfoById ($_GET[ADMIN_ACTION_ID], self::$objCategoryTableFName);
                $objWhereToMove = $this->getCategoryInfoById ($_GET[ADMIN_ACTION_TO], self::$objCategoryTableFName);
                // Get the node subtree, that's move, make sure the node we move to ain't a child;
                $objMovedNodeSubTree = self::$objMPTT->mpttGetTree ($objThatIsMoved);

                // Memorize;
                $objIsChild = new B (FALSE);
                foreach ($objMovedNodeSubTree as $k => $v) {
                     if ($v[self::$objMPTT->objNameOfNode] == $objWhereToMove) {
                         $objIsChild->switchType ();
                     }
                }

                // Check if it's a child or not;
                if ($objIsChild->toBoolean () == TRUE) {
                    // Set an error message;
                    self::$objAdministration->setErrorMessage (new S (PRODUCTS_CATEGORY_CANNOT_BE_MOVED),
                    $objURLToGoBack);
                } else {
                    // Move nodes;
                    self::$objMPTT->mpttMoveNode ($objThatIsMoved,
                    $objWhereToMove, $_GET[ADMIN_ACTION_TYPE]);
                    $this->setHeaderKey ($objURLToGoBack, new S ('Location'));
                }
                // Break out ...
                break;

            case 'categoryMoveOperation':
                // The URL to go back too;
                $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_SUBPAGE)), new A (Array (MANAGE_PRODUCTS)));

                // Do some work;
                ($this->checkPOST ()->toBoolean () == TRUE) ?
                ($objOLDCategoryId = $this->getPOST (new S ('old_category_id'))) :
                ($objOLDCategoryId = new S ('0'));

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (PRODUCTS_MOVE_PRODUCTS))
                ->setSQLAction (new S ('update'))
                ->setTableName (self::$objProductsTable)
                ->setUpdateField (self::$objProductsTableFId)
                // Specific code here, need abstractization!
                ->setUpdateWhere ($this->doModuleToken (_S ('%objProductsTableFCategoryId = "%Id"')
                ->doToken ('%Id', $objOLDCategoryId)))
                ->setName ($objFormToRender)
                ->setRedirect ($objURLToGoBack)
                ->setInputType (new S ('submit'))
                ->setValue (new S (PRODUCTS_MOVE_PRODUCTS))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('old_category_id'))
                ->setLabel (new S (PRODUCTS_OLD_CATEGORY))
                ->setContainerDiv (new B (TRUE));

                // Cateories;
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique ($v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Categories;
                $this->setInputType (new S ('select'))
                ->setName (self::$objProductsTableFCategoryId)
                ->setLabel (new S (PRODUCTS_NEW_CATEGORY))
                ->setContainerDiv (new B (TRUE));
                foreach ($this->getCategories () as $k => $v) {
                    $this->setInputType (new S ('option'))
                    ->setName ($v[self::$objMPTT->objIdField])
                    ->setValue ($v[self::$objMPTT->objIdField])
                    ->setLabel (new S (str_repeat ('--' . _SP,
                    (int) $v['depth']->toString ()) .
                    MPTT::mpttRemoveUnique ($v[self::$objMPTT
                    ->objNameOfNode])));
                }

                // Continue;
                $this->setFormEndAndExecute (new B (TRUE));
                break;

            case 'configurationEdit':
                // Do some work;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    $objURLToGoBack = URL::rewriteURL (new A (Array (ADMIN_ACTION)),
                    new A (Array ($this->getPOST (new S ('what')))));
                }

                // Do the form, make it happen;
                $this->setMethod (new S ('POST'))
                ->setFieldset (new S (PRODUCTS_MANAGE_CONFIGURATION))
                ->setName ($objFormToRender);

                // Set redirect;
                if ($this->checkPOST ()->toBoolean () == TRUE) {
                    $this->setRedirect ($objURLToGoBack);
                }

                // Continue;
                $this->setInputType (new S ('submit'))
                ->setValue (new S (ADMIN_ACTION))
                ->setInputInfoMessage ($this->getHELP ($objFormToRender))
                ->setContainerDiv (new B (TRUE))
                ->setInputType (new S ('select'))
                ->setName (new S ('what'))
                ->setLabel (new S (PRODUCTS_CONFIG_CHOOSE))
                ->setContainerDiv (new B (TRUE))
                ->setFormEndAndExecute (new B (TRUE));
                break;
        }
    }
}
?>
