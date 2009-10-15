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

### MPTT: Modified Pre-Order Tree Traversal; (the guru of Hierarchy algorithms);
interface IFaceMPTT {
    public function mpttAddNode (S $objAddedNode, S $objNode, S $objAddLeftRight = NULL);
    public function mpttRemoveNode (S $objNodeToRemove, B $objRemoveRecursive = NULL);
    public function mpttGetTreeDepth (S $objNode, S $objDepth = NULL);
    public function mpttGetTree (S $objSubNode = NULL);
    public function mpttCheckIfNodeExists (S $objCheckedNode);
    public function mpttGetSinglePath (S $objNodeFrom);
    public function mpttGetTreeLeafs ();
    public static function mpttAddUnique (S $objNodeName, S $objNodeTimestamp);
    public static function mpttRemoveUnique (S $objNodeName);
}

### Cookie: The cookie (or session) storage mechanism ...
interface IFaceCookieStorage {
    public function setKey (S $objCookieSessionKey, S $objCookieSessionVariable, B $objExpireTime);
    public function getKey (S $objCookieSessionKey);
    public function unSetKey (S $objKey);
    public function checkKey (S $objKey);
}

### MAIL: The MAIL PHP class, we use constantly ...
interface IFaceMAIL {
    public function setFrom (S $objFromMAIL);
    public function setReplyTo (S $objReplyTo);
    public function setAttachment (FilePath $objEMLAttachment, S $objAttachmentType);
    public function doMAIL (S $objMAILTo, S $objMAILSubject, S $objMAILContent);
}

/**
 * MPTT: Modified Pre-Order Tree Traversal, a generic algorithm that allows us to have tree like data structures. Among all known
 * hierarchy algorhytms like the adjency list, the parent_id way or any other, the MPTT proves itself as the best hierarchy algorithm
 * to date, looking at the parent/child implementation like a serioes of left and right values;
 *
 * @package RA-MPTT-Hierarchy
 * @category RA-Revolutionary-Algorithms
 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
 * @copyright Under the terms of the GNU General Public License v3
 * @version $Id: 16_ALG.php 335 2009-10-12 12:21:47Z catalin.zamfir $
 * @since Version 1.0
 * @access public
 */
class MPTT extends SQL implements IFaceMPTT {
    // TABLE: Fields;
    public $objIdField                          = NULL;
    public $objRightField                       = NULL;
    public $objLeftyField                       = NULL;
    public $objNameOfNode                       = NULL;
    public $objSEOName                          = NULL;
    public $objNodeDate                         = NULL;

    // TABLE Privates;
    private $objTable                           = NULL;
    private $objSilentIgnr                      = NULL;

    // CONSTANTS;
    const FIRST_CHILD                           = 1;
    const LAST_CHILD                            = 2;
    const PREVIOUS_BROTHER                      = 3;
    const NEXT_BROTHER                          = 4;

    // CONSTRUCT;
    public function __construct (S $objTable, S $objRootNodeRL = NULL, B $objSilentIgnr = NULL,
    S $objNameOfNode = NULL, S $objRightField = NULL, S $objLeftyField = NULL,
    S $objIdField = NULL, S $objSEOName = NULL, S $objNodeDate = NULL) {

        // Set some predefines;
        if ($objRightField == NULL) { $objRightField = new S ('rgt');   }
        if ($objLeftyField == NULL) { $objLeftyField = new S ('lft');   }
        if ($objNameOfNode == NULL) { $objNameOfNode = new S ('name');  }
        if ($objSEOName    == NULL) { $objSEOName    = new S ('seo');   }
        if ($objNodeDate   == NULL) { $objNodeDate   = new S ('date');  }
        if ($objRootNodeRL == NULL) { $objRootNodeRL = new S ('/');     }
        if ($objIdField    == NULL) { $objIdField    = new S ('id');    }
        if ($objSilentIgnr == NULL) { $objSilentIgnr = new B (FALSE);   }

        // Set the requiered parameters to work with;
        $this->objTable      = $objTable;
        $this->objIdField    = $objIdField;
        $this->objRightField = $objRightField;
        $this->objLeftyField = $objLeftyField;
        $this->objNameOfNode = $objNameOfNode;
        $this->objSEOName    = $objSEOName;
        $this->objNodeDate   = $objNodeDate;
        $this->objSilentIgnr = $objSilentIgnr;

        // Add the ROOT node;
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', $this->objTable)
        ->doToken ('%condition', NULL))->doCount ()->toInt () == 0) {
            // Add an SQL condition;
            $objSQLCondition = new S ('%objNameOfNode = "%nId",
            %objLeftyField = "1", %objRightField = "2",
            %objSEOName = "%sId", %objNodeDate = "%dId"');

            // Add it, cause the table's empty;
            $this->_Q (_QS ('doINSERT')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', $objSQLCondition)
            ->doToken ('%nId', $objRootNodeRL)
            ->doToken ('%sId', URL::getURLFromString ($objRootNodeRL))
            ->doToken ('%dId', $_SERVER['REQUEST_TIME']));
        }
    }

    /**
    * Will replace module tokens (also named table fields) that can be used independent of the table structure. We provide the ORM
	* as a series of tokens that can be replaced inside the query string. We provide such a mechanism for the MPTT object also;
    *
    * @param S $objSQLParam The SQL string to be processed
    * @return S Will return the current SQL string with modified tokens
	* @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	* @copyright Under the terms of the GNU General Public License v3
	* @version $Id: 16_ALG.php 335 2009-10-12 12:21:47Z catalin.zamfir $
	* @since Version 1.0
	* @access public
    */
    public function doModuleToken (S $objSQLParam) {
        // Set the tokens to be replaced;
        $objTokens = new A;
        $objTokens[1] = 'objTable';
        $objTokens[2] = 'objIdField';
        $objTokens[3] = 'objRightField';
        $objTokens[4] = 'objLeftyField';
        $objTokens[5] = 'objNameOfNode';
        $objTokens[6] = 'objSEOName';
        $objTokens[7] = 'objNodeDate';

        // Set the replacements;
        $objReplac = new A;
        $objReplac[1] = $this->objTable;
        $objReplac[2] = $this->objIdField;
        $objReplac[3] = $this->objRightField;
        $objReplac[4] = $this->objLeftyField;
        $objReplac[5] = $this->objNameOfNode;
        $objReplac[6] = $this->objSEOName;
        $objReplac[7] = $this->objNodeDate;

        // Do a CALL to your parents;
        return parent::doModuleTokens ($objTokens, $objReplac, $objSQLParam);
    }

    /**
     * Will get node information by the field id. This method is used to return the information from a hierarchy table (a category
	 * table for example). This can be a shortcut method to any module specific get(*)ById specific methods;
     *
     * @param S $objNodeId The node id to query for
     * @param S $objFieldToGet The field to get (specific to the ones of the MPTT object)
     * @return M Depends on what was requested
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 16_ALG.php 335 2009-10-12 12:21:47Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
     */
    public function mpttGetNodeInfoById (S $objNodeId, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objIdField = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objNodeId))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will get node information by name. This method is used to return the information from a hierarchy table (a category table for
	 * example). This can be a shortcut method to any module specific get(*)ByName specific methods;
     *
     * @param S $objNodeId The node name to query for
     * @param S $objFieldToGet The field to get (specific to the ones of the MPTT object)
     * @return M Depends on what was requested
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 16_ALG.php 335 2009-10-12 12:21:47Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
     */
    public function mpttGetNodeInfoByName (S $objNodeName, S $objFieldToGet) {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $objFieldToGet)->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objNameOfNode = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objNodeName))->offsetGet (0)->offsetGet ($objFieldToGet);
    }

    /**
     * Will check if the current given node exists. This method is used to pre-check if a node already exists, because if we don't
	 * do that we could end-up with two categories of the same name which mean errors in our system;
     *
     * @param S $objCheckedNode The checked node
     * @return B Will return true if the node exists
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 16_ALG.php 335 2009-10-12 12:21:47Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
     */
    public function mpttCheckIfNodeExists (S $objCheckedNode) {
        if ($this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objNameOfNode = "%Id" LIMIT 1'))
        ->doToken ('%Id', $objCheckedNode))->doCount ()->toInt () != 0) {
            // Do return ...
            return new B (TRUE);
        } else {
            // Do return ...
            return new B (FALSE);
        }
    }

    /**
     * Will add the node, relating it to the "parent" node, by checking the third parameter to see how to add the node. This method
	 * is used with one of the adding CONSTANTS to specify how to add the child node to the parent node;
     *
     * @param S $objANode The node do add
     * @param S $objPNode The related node as parent or brother
     * @param S $objAddLeftRight The kind of adding to be done
     * @return B Will return true if the node was added
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 16_ALG.php 335 2009-10-12 12:21:47Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
     */
    public function mpttAddNode (S $objANode, S $objPNode, S $objAddLeftRight = NULL) {
        // Set some predefines;
        if ($objAddLeftRight == NULL) { $objAddLeftRight = new S ((string) self::FIRST_CHILD); }

        // Check that the inserted node is unique;
        if ($this->mpttCheckIfNodeExists ($objANode)->toBoolean () == TRUE) {
            if ($this->objSilentIgnr->toBoolean () == FALSE) {
                // Send an error if the node is not unique;
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (MPTT_NODE_EXISTS),
                new S (MPTT_NODE_EXISTS_FIX));
            } else {
                // Return FALSE, don't allow further execution;
                return new B (FALSE);
            }
        } else {
            // Do a switch on the type of node;
            switch ((int) $objAddLeftRight->toString ()) {
                // Add as the first child of;
                case MPTT::FIRST_CHILD:
                    $this->mpttNewFirstChild ($objANode, $objPNode);
                    break;

                // Or the last child of;
                case MPTT::LAST_CHILD:
                    $this->mpttNewLastChild ($objANode, $objPNode);
                    break;

                // We can even do a previous brother;
                case MPTT::PREVIOUS_BROTHER:
                    $this->mpttNewPrevSibling ($objANode, $objPNode);
                    break;

                // Or a next one;
                case MPTT::NEXT_BROTHER:
                    $this->mpttNewNextSibling ($objANode, $objPNode);
                    break;
            }

            // Do return ...
            return new B (TRUE);
        }
    }

    /**
     * Will move a node to a destination node. Given a node from where to move to where to move, this method will move the specified
	 * node to a new destination. You can check the source of this method to see how it's made;
     *
     * @param S $objNodeName The node to move
     * @param S $objNodePName There to move to
     * @param S $objMoveType What kind of move
     * @return B Will return TRUE if the node was moved
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 16_ALG.php 335 2009-10-12 12:21:47Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
     */
    public function mpttMoveNode (S $objNodeName, S $objNodePName, S $objMoveType) {
        // Switch ...
        switch ($objMoveType) {
            case '1':
                $this->mpttMoveToFirstChild
                ($objNodeName, $objNodePName);
                break;

            case '2':
                $this->mpttMoveToLastChild
                ($objNodeName, $objNodePName);
                break;

            case '3':
                $this->mpttMoveToPrevSibling
                ($objNodeName, $objNodePName);
                break;

            case '4':
                $this->mpttMoveToNextSibling
                ($objNodeName, $objNodePName);
                break;
        }

        // Do return ...
        return new B (TRUE);
    }

    /**
     * Will remove a node, either recursive or by promotion. This method, given a node name to remove, will search for that node
	 * and will remove it either by promotion (promoting the childs to a root LEVEL) - or by recursivelly deleting its childs;
     *
     * @param S $objNodeToRemove The node to remove
     * @param B $objRemoveRecursive Remove either recursive, or promote children
     * @return B Will return true if the node is removed
	 * @author Catalin Z. Alexandru <catalin.zamfir@raphpframework.ro>
	 * @copyright Under the terms of the GNU General Public License v3
	 * @version $Id: 16_ALG.php 335 2009-10-12 12:21:47Z catalin.zamfir $
	 * @since Version 1.0
	 * @access public
     */
    public function mpttRemoveNode (S $objNodeToRemove, B $objRemoveRecursive = NULL) {
        // Set some predefines;
        if ($objRemoveRecursive == NULL) { $objRemoveRecursive = new B (FALSE); }

        // Determine if the node we want to delete is a LEAF node, or not!
        $objLeafNodes = $this->mpttGetTreeLeafs ();
        $objIsALeafNd = new B (FALSE);

        // Parse the array;
        foreach ($objLeafNodes as $k => $v) {
            if ($v[$this->objNameOfNode] == $objNodeToRemove) {
                $objIsALeafNd->switchType ();
            }
        }

        // Get some node information;
        $objQ = $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('*'))->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objNameOfNode = "%nId"'))
        ->doToken ('%nId', $objNodeToRemove));

        // Set left, right and width;
        $objLefty = $objQ->offsetGet (0)->offsetGet ($this->objLeftyField);
        $objRight = $objQ->offsetGet (0)->offsetGet ($this->objRightField);
        $objWidth = new S ((string) ((int) $objRight->toString () - (int) $objLefty->toString () + 1));

        // Check if we do a recursive delete, or promotion delete;
        if (($objIsALeafNd->toBoolean () == TRUE) || ($objRemoveRecursive->toBoolean () == TRUE)) {
            // Do the node deletion;
            $this->_Q (_QS ('doDELETE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objLeftyField BETWEEN %LowerLimit AND %UpperLimit'))
            ->doToken ('%LowerLimit', $objLefty)->doToken ('%UpperLimit', $objRight));

            // Update the right-hand side;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objRightField = %objRightField - %LimitWidth
            WHERE %objRightField > %UpperLimit'))
            ->doToken ('%LimitWidth', $objWidth)->doToken ('%UpperLimit', $objRight));

            // Update the lefty-hand side;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objLeftyField = %objLeftyField - %LimitWidth
            WHERE %objLeftyField > %UpperLimit'))
            ->doToken ('%LimitWidth', $objWidth)->doToken ('%UpperLimit', $objRight));

            // Do return ...
            return new B (TRUE);
        } else {
            // Do the node deletion;
            $this->_Q (_QS ('doDELETE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objLeftyField = %LowerLimit'))
            ->doToken ('%LowerLimit', $objLefty));

            // If we removed, promote kids;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objRightField =
            %objRightField - 1, %objLeftyField = %objLeftyField - 1
            WHERE %objLeftyField BETWEEN %LowerLimit AND %UpperLimit'))
            ->doToken ('%LowerLimit', $objLefty)->doToken ('%UpperLimit', $objRight));

            // Update the right-hand side;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objRightField =
            %objRightField - 2 WHERE %objRightField > %UpperLimit'))
            ->doToken ('%UpperLimit', $objRight));

            // Update the lefty-hand side;
            $this->_Q (_QS ('doUPDATE')
            ->doToken ('%table', $this->objTable)
            ->doToken ('%condition', new S ('%objLeftyField =
            %objLeftyField - 2 WHERE %objLeftyField > %UpperLimit'))
            ->doToken ('%UpperLimit', $objRight));

            // Do return ...
            return new B (TRUE);
        }
    }

    /**
     * Will return an array of the node, or subnode if passed.
     *
     * This method will retrieve an array with the necessary fields, and depth of each node. This information can be used to show
     * a tree like structure using PHP parsing and some clever echoing;
     *
     * @param S $objSubNode The subnode to get the tree from, if passed;
     * @param S $objSQLConditionOrder The kind of ordering to do, ASC or DESC;
     * @return array Will return the result array;
     */
    public function mpttGetTree (S $objSubNode = NULL, S $objSQLConditionOrder = NULL) {
        if ($objSubNode == NULL) {
            // Set some predefines;
            if ($objSQLConditionOrder == NULL) $objSQLConditionOrder = new S ('ASC');

            // Do a BIG condition;
            $objSQLCondition = new S;
            $objSQLCondition->appendString (_SP)->appendString ('AS n, %table AS p');
            $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
            $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
            $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField %condition');

            // Do return ...
            return $this->_Q (_QS ('doSELECT')
            ->doToken ('%what', new S ('n.id, n.%objNameOfNode, (COUNT(p.%objNameOfNode) - 1) AS depth'))
            ->doToken ('%condition', $objSQLCondition)->doToken ('%table', $this->objTable)
            ->doToken ('%condition', $objSQLConditionOrder));
        } else {
            // Do a BIG condition;
            $objSQLCondition = new S;
            $objSQLCondition->appendString (_SP)->appendString ('AS n, %table AS p, %table AS s,');
            $objSQLCondition->appendString (_SP)->appendString ('(SELECT n.%objNameOfNode,(COUNT(p.%objNameOfNode) - 1) AS depth');
            $objSQLCondition->appendString (_SP)->appendString ('FROM %table AS n, %table AS p');
            $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
            $objSQLCondition->appendString (_SP)->appendString ('AND n.%objNameOfNode = "%nId"');
            $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
            $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField) AS t');
            $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
            $objSQLCondition->appendString (_SP)->appendString ('AND n.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('BETWEEN s.%objLeftyField');
            $objSQLCondition->appendString (_SP)->appendString ('AND s.%objRightField');
            $objSQLCondition->appendString (_SP)->appendString ('AND s.%objNameOfNode = t.%objNameOfNode');
            $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
            $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField');

            // Do return ...
            return $this->_Q (_QS ('doSELECT')
            ->doToken ('%what', new S ('n.%objIdField, n.%objNameOfNode, (COUNT(p.%objNameOfNode) - (t.depth + 1)) AS depth'))
            ->doToken ('%condition', $objSQLCondition)->doToken ('%table', $this->objTable)->doToken ('%nId', $objSubNode));
        }
    }

    /**
     * Will return the path from the current node to the root;
     *
     * This method will get the path from the current node to the root node. This can be used to make a breadcrum like trail,
     * for the user to work with;
     *
     * @param S $objNodeFrom The node to get the path from'
     * @return array Will return the path array from the current node;
     */
    public function mpttGetSinglePath (S $objNodeFrom) {
        // Do a BIG condition;
        $objSQLCondition = new S;
        $objSQLCondition->appendString (_SP)->appendString ('AS n, %table AS p');
        $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
        $objSQLCondition->appendString (_SP)->appendString ('AND n.%objNameOfNode = "%nId"');
        $objSQLCondition->appendString (_SP)->appendString ('ORDER BY p.%objLeftyField');

        // Return the path to the root;
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('p.%objNameOfNode'))->doToken ('%condition', $objSQLCondition)
        ->doToken ('%table', $this->objTable)->doToken ('%nId', $objNodeFrom));
    }

    /**
     * Will return the tree depth of the current node;
     *
     * This method will return the depth of the tree from the passed node downwards. This can be used to determine imediate leafs,
     * or how to actually show the tree;
     *
     * @param S $objNode The node to query for;
     * @param S $objDepth The depth to retrieve;
     * @return array The result array;
     */
    public function mpttGetTreeDepth (S $objNode, S $objDepth = NULL) {
        // Set some predefined defaults;
        if ($objDepth == NULL) { $objDepth = new S ('1'); }

        // Do a BIG condition;
        $objSQLCondition = new S;
        $objSQLCondition->appendString (_SP)->appendString ('AS n, %table AS p, %table AS s,');
        $objSQLCondition->appendString (_SP)->appendString ('(SELECT n.%objNameOfNode, (COUNT(p.%objNameOfNode) - 1) AS depth');
        $objSQLCondition->appendString (_SP)->appendString ('FROM %table AS n, %table AS p');
        $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
        $objSQLCondition->appendString (_SP)->appendString ('AND n.%objNameOfNode = "%nId"');
        $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
        $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField) AS t');
        $objSQLCondition->appendString (_SP)->appendString ('WHERE n.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('BETWEEN p.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('AND p.%objRightField');
        $objSQLCondition->appendString (_SP)->appendString ('AND n.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('BETWEEN s.%objLeftyField');
        $objSQLCondition->appendString (_SP)->appendString ('AND s.%objRightField');
        $objSQLCondition->appendString (_SP)->appendString ('AND s.%objNameOfNode = t.%objNameOfNode');
        $objSQLCondition->appendString (_SP)->appendString ('GROUP BY n.%objNameOfNode');
        $objSQLCondition->appendString (_SP)->appendString ('HAVING depth <= %dId');
        $objSQLCondition->appendString (_SP)->appendString ('ORDER BY n.%objLeftyField');

        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', new S ('n.%objNameOfNode, (COUNT(p.%objNameOfNode) - (t.depth + 1)) AS depth'))
        ->doToken ('%condition', $objSQLCondition)->doToken ('%table', $this->objTable)
        ->doToken ('%dId', $objDepth)->doToken ('%nId', $objNode));
    }

    /**
     * Will get the leafs of the current tree;
     *
     * This method will query the databse and get only the leafs of the current tree, that means the edges of our tree like
     * structure, which can be used for example to determine if the node we remove is a leaf node or not, removing it recursive or
     * not. Other uses can for exampe be used on categories, to get extremeties of those categories;
     *
     * @return array The result array;
     */
    public function mpttGetTreeLeafs () {
        // Do return ...
        return $this->_Q (_QS ('doSELECT')
        ->doToken ('%what', $this->objNameOfNode)->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('WHERE %objLeftyField = %objRightField - 1')));
    }

    /**
     * Will remove the unique "string identifier" added on the name of a node to make it unique in the hierarchy. The string is
     * actually the UNIX timestamp identifier, as the chances of making two categories with the same name at the same time, in a
     * future transactional SQL are close to none;
     */
    public static final function mpttRemoveUnique (S $objNodeName) {
        // Do return ...
        return $objNodeName
        ->eregReplace ('-uniQ-[0-9]*', _NONE);
    }

    /**
     * Will add the unique string identifier to the node name, which willa llow us to have unique category names in an independent
     * manner. We use the UNIX timestamp as an unique identifier as the changes of actually having something like that in our code
     * happening at the same time is close to 0;
     */
    public static final function mpttAddUnique (S $objNodeName, S $objNodeTimestamp) {
        if ($objNodeName->findIPos ('-uniQ-') instanceof B) {
            // Do return ...
            return $objNodeName
            ->appendString ('-uniQ-%Id')
            ->doToken ('%Id', $objNodeTimestamp);
        } else {
            // Nada ...
            return $objNodeName;
        }
    }

    /**
     * Will add the node as the first child of the parent node;
     *
     * This method will determine the parent node data it needs to add the current given node as the first child of the
     * passed parent node;
     *
     * @param S $objNodeName The node name to add;
     * @param S $objNodePName The parent node to relate to;
     */
    private function mpttNewFirstChild (S $objNodeName, S $objNodePName) {
        // Get some information from them;
        $objPLefty = new S ((string) ((int) $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objLeftyField)
        ->toString () + 1));

        $objPRight = new S ((string) ((int) $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objLeftyField)
        ->toString () + 2));

        // Update required;
        $this->shiftRL ($objPLefty, new S ('2'));

        // Make the new node;
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objNameOfNode = "%nId", %objSEOName = "%uId",
        %objLeftyField = "%fId", %objRightField = "%sId", %objNodeDate = "%dId"'))
        ->doToken ('%nId', $objNodeName)
        ->doToken ('%fId', $objPLefty)
        ->doToken ('%sId', $objPRight)
        ->doToken ('%uId', URL::getURLFromString ($objNodeName))
        ->doToken ('%dId', $_SERVER['REQUEST_TIME']));
    }

    /**
     * Will add the node as the last child of the parent node;
     *
     * This method will determine the parent node data it needs to add the current given node as the last child of the
     * passed parent node;
     *
     * @param S $objNodeName The node name to add;
     * @param S $objNodePName The parent node to relate to;
     */
    private function mpttNewLastChild (S $objNodeName, S $objNodePName) {
        // Get some information from them;
        $objPLefty = new S ((string) ((int) $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objRightField)
        ->toString () + 0));

        $objPRight = new S ((string) ((int) $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objRightField)
        ->toString () + 1));

        // Update required;
        $this->shiftRL ($objPLefty, new S ('2'));

        // Make the new node;
        // Make the new node;
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objNameOfNode = "%nId", %objSEOName = "%uId",
        %objLeftyField = "%fId", %objRightField = "%sId", %objNodeDate = "%dId"'))
        ->doToken ('%nId', $objNodeName)
        ->doToken ('%fId', $objPLefty)
        ->doToken ('%sId', $objPRight)
        ->doToken ('%uId', URL::getURLFromString ($objNodeName))
        ->doToken ('%dId', $_SERVER['REQUEST_TIME']));
    }

    /**
     * Will add the node as the previous sibling of the parent node;
     *
     * This method will determine the parent node data it needs to add the current given node as the previous sibling of the
     * passed parent node;
     *
     * @param S $objNodeName The node name to add;
     * @param S $objNodePName The parent node to relate to;
     */
    private function mpttNewPrevSibling (S $objNodeName, S $objNodePName) {
        // Get some information from them;
        $objPLefty = new S ((string) ((int) $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objLeftyField)
        ->toString () + 0));

        $objPRight = new S ((string) ((int) $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objLeftyField)
        ->toString () + 1));

        // Update required;
        $this->shiftRL ($objPLefty, new S ('2'));

        // Make the new node;
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objNameOfNode = "%nId", %objSEOName = "%uId",
        %objLeftyField = "%fId", %objRightField = "%sId", %objNodeDate = "%dId"'))
        ->doToken ('%nId', $objNodeName)
        ->doToken ('%fId', $objPLefty)
        ->doToken ('%sId', $objPRight)
        ->doToken ('%uId', URL::getURLFromString ($objNodeName))
        ->doToken ('%dId', $_SERVER['REQUEST_TIME']));
    }

    /**
     * Will add the node as the next sibling of the parent node;
     *
     * This method will determine the parent node data it needs to add the current given node as the next sibling of the
     * passed parent node;
     *
     * @param S $objNodeName The node name to add;
     * @param S $objNodePName The parent node to relate to;
     */
    private function mpttNewNextSibling (S $objNodeName, S $objNodePName) {
        // Get some information from them;
        $objPLefty = new S ((string) ((int) $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objRightField)
        ->toString () + 1));

        $objPRight = new S ((string) ((int) $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objRightField)
        ->toString () + 2));

        // Update required;
        $this->shiftRL ($objPLefty, new S ('2'));

        // Make the new node;
        // Make the new node;
        $this->_Q (_QS ('doINSERT')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objNameOfNode = "%nId", %objSEOName = "%uId",
        %objLeftyField = "%fId", %objRightField = "%sId", %objNodeDate = "%dId"'))
        ->doToken ('%nId', $objNodeName)
        ->doToken ('%fId', $objPLefty)
        ->doToken ('%sId', $objPRight)
        ->doToken ('%uId', URL::getURLFromString ($objNodeName))
        ->doToken ('%dId', $_SERVER['REQUEST_TIME']));
    }

    /**
     * Will move the given node as the previous sibling;
     *
     * This method will move the given node as the previous sibling of the "parent" node given as a whole branch. It won't do a
     * move by promotion, because that means re-arranging the whole tree one by one, while massive moves are more common in this
     * industry, or massive operations of the tree;
     *
     * @param S $objNodeName The node to move;
     * @param S $objNodePName The destination node;
     * @return boolean Will return true if was able to move the node;
     */
    private function mpttMoveToPrevSibling (S $objNodeName, S $objNodePName) {
        return $this->moveSubTree ($objNodeName, $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objLeftyField));
    }

    /**
     * Will move the given node as the last child;
     *
     * This method will move the given node as the last child of the "parent" node given as a whole branch. It won't do a
     * move by promotion, because that means re-arranging the whole tree one by one, while massive moves are more common in this
     * industry, or massive operations of the tree;
     *
     * @param S $objNodeName The node to move;
     * @param S $objNodePName The destination node;
     * @return boolean Will return true if was able to move the node;
     */
    private function mpttMoveToLastChild (S $objNodeName, S $objNodePName) {
        $this->moveSubTree ($objNodeName, $this
        ->mpttGetNodeInfoByName ($objNodePName, $this->objRightField));
    }

    /**
     * Will move the given node as the next sibling;
     *
     * This method will move the given node as the next sibling of the "parent" node given as a whole branch. It won't do a
     * move by promotion, because that means re-arranging the whole tree one by one, while massive moves are more common in this
     * industry, or massive operations of the tree;
     *
     * @param S $objNodeName The node to move;
     * @param S $objNodePName The destination node;
     * @return boolean Will return true if was able to move the node;
     */
    private function mpttMoveToNextSibling (S $objNodeName, S $objNodePName) {
        $this->moveSubTree ($objNodeName, new S ((string) ((int)
        $this->mpttGetNodeInfoByName ($objNodePName, $this->objRightField)->toString () + 1)));
    }

    /**
     * Will move the given node as the first child;
     *
     * This method will move the given node as the first child of the "parent" node given as a whole branch. It won't do a
     * move by promotion, because that means re-arranging the whole tree one by one, while massive moves are more common in this
     * industry, or massive operations of the tree;
     *
     * @param S $objNodeName The node to move;
     * @param S $objNodePName The destination node;
     * @return boolean Will return true if was able to move the node;
     */
    private function mpttMoveToFirstChild (S $objNodeName, S $objNodePName) {
        $this->moveSubTree ($objNodeName, new S ((string) ((int)
        $this->mpttGetNodeInfoByName ($objNodePName, $this->objLeftyField)->toString () + 1)));
    }

    /**
     * Will shift left and right values;
     *
     * This method will update left and right values. This method is used to do updates on adding new nodes or moving/deletion
     * of those nodes;
     *
     * @param S $objFirst Where to start;
     * @param S $objLast Where to end;
     */
    private function shiftRL (S $objFirst, S $objDelta) {
        $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objLeftyField = %objLeftyField + %dId
        WHERE %objLeftyField >= %fId'))->doToken ('%dId', $objDelta)->doToken ('%fId', $objFirst));

        $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objRightField = %objRightField + %dId
        WHERE %objRightField >= %fId'))->doToken ('%dId', $objDelta)->doToken ('%fId', $objFirst));
    }

    /**
     * Will shift left and right values in a range;
     *
     * This method will update left and right values that are in a given range. This method is used to do updates on adding new
     * nodes or moving/deletion of those nodes;
     *
     * @param S $objFirst Where to start;
     * @param S $objLast Where to end;
     * @param S $objDelta What delta to use for size;
     */
    private function shiftRLRange (S $objFirst, S $objLast, S $objDelta) {
        $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objLeftyField = %objLeftyField + %dId WHERE %objLeftyField >= %fId
        AND %objLeftyField <= %eId'))->doToken ('%dId', $objDelta)->doToken ('%fId', $objFirst)->doToken ('%eId', $objLast));

        $this->_Q (_QS ('doUPDATE')
        ->doToken ('%table', $this->objTable)
        ->doToken ('%condition', new S ('%objRightField = %objRightField + %dId WHERE %objRightField >= %fId
        AND %objRightField <= %eId'))->doToken ('%dId', $objDelta)->doToken ('%fId', $objFirst)->doToken ('%eId', $objLast));
    }

    /**
     * Will move a subtree to a new destination;
     *
     * This method will move a given subtree from the given node, to a new destination, indicated by the left or right delta field
     * we pass as the second parameter. Moves are quite easy when taken as differences between left or right;
     *
     * @param S $objNodeName The node name to move;
     * @param S $objNodeTo The passed delta (difference);
     */
    private function moveSubTree (S $objNodeName, S $objNodeTo) {
        // Get some info from them;
        $objNodeNamePLefty = $this->mpttGetNodeInfoByName ($objNodeName, $this->objLeftyField);
        $objNodeNamePRight = $this->mpttGetNodeInfoByName ($objNodeName, $this->objRightField);

        // Get the tree size;
        $objTreeSize = new S ((string) ((int) $objNodeNamePRight
        ->toString () - (int) $objNodeNamePLefty->toString () + 1));

        // Shifting;
        $this->shiftRL ($objNodeTo, $objTreeSize);

        // If ...;
        if ((int) $objNodeNamePLefty->toString () > (int) $objNodeTo->toString ()) {
            $objNodeNamePLefty = new S ((string) ((int) $objNodeNamePLefty
            ->toString () + (int) $objTreeSize->toString ()));

            $objNodeNamePRight = new S ((string) ((int) $objNodeNamePRight
            ->toString () + (int) $objTreeSize->toString ()));
        }

        // Shifting;
        $this->shiftRLRange ($objNodeNamePLefty, $objNodeNamePRight,
        $objDelta = new S ((string) ((int) $objNodeTo
        ->toString () - (int) $objNodeNamePLefty->toString ())));
        $this->shiftRL (new S ((string) ((int) $objNodeNamePRight->toString () + 1)),
        new S ((string) (-1 * (int) $objTreeSize->toString ())));
    }
}

class CookieStorage implements IFaceCookieStorage {
    private $objObjectCookie                    = NULL;
    private $objProjectString                   = NULL;

    # CONSTRUCT;
    public function __construct (IFaceCommonConfigExtension & $objObjectCookie) {
        // Sanitize the project name;
        $this->objProjectString = new S (PROJECT_NAME);
        $this->objProjectString->doToken (array (_SP, ',', ';', '='), _U);

        // Tie in with the object that requests the cookie;
        $this->objObjectCookie = $objObjectCookie;
    }

    /**
     * Will set the given cookie information;
     *
     * This method will set the key/var pair of cookie information needed for the given module. In case of _SESSION it will store
     * the information by arrays, while cookies will have an identifier string that's long enough to be unique. Each module that
     * requests a cookie object has its own cookie scope;
     *
     * @param S $objKey The key to set;
     * @param S $objContent The content to set;
     * @param B $objExpTime The expire time to take in account;
     */
    public function setKey (S $objKey, S $objContent, B $objExpTime) {
        // Switch ...
        switch ($this->objObjectCookie->getConfigKey (new S ('authentication_cookie_store_type'))) {
            case 'cookie':
                // Set the expire time, to current + SESSION_COOKIE_LIFETIME;
                ($objExpTime->toBoolean () != TRUE) ? ($expTme = new I (0)) :
                ($expTme = new I ($_SERVER['REQUEST_TIME'] + SESSION_COOKIE_LIFETIME));

                // Trigger the browser cookie setter, and set the proper key/vars;
                setcookie ($this->objProjectString . _U . $this->objObjectCookie->getObjectCLASS ()
                . _U . $objKey, $objContent, $expTme->toInt (), new S ('/'));

                // Redirect to same page, each time;
                TPL::setHeaderKey (URL::rewriteURL (), new S ('Location'));
                break;

            case 'session':
                // Just use the _SESSION to store it;
                $_SESSION[$this->objProjectString->toString ()]
                [$this->objObjectCookie->getObjectCLASS ()->toString ()][$objKey->toString ()] = $objContent;
                break;

            default:
                // Make an error on it, cause yeah ...
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (SESSION_TYPE_ERROR),
                new S (SESSION_TYPE_ERROR_FIX));
                break;
        }
    }

    /**
     * Will get information from the cookie storage;
     *
     * This method will return the key you want to request information for. Information should first be checked for existence, or
     * else an error screen will pop-up, saying that the requested array key was not found.
     *
     * @param S $objKey The key to get information from;
     * @return mixed Depends on what was requested;
     */
    public function getKey (S $objKey) {
        switch ($this->objObjectCookie->getConfigKey (new S ('authentication_cookie_store_type'))) {
            case 'cookie':
                // Do return ...
                return new S ($_COOKIE[$this->objProjectString . _U . $this->objObjectCookie
                ->getObjectCLASS () . _U . $objKey]);
                break;

            case 'session':
                // Do return ...
                return $_SESSION[$this->objProjectString->toString ()]
                [$this->objObjectCookie->getObjectCLASS ()->toString ()][$objKey->toString ()];
                break;

            default:
                // Make an error on it, cause yeah ...
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (SESSION_TYPE_ERROR),
                new S (SESSION_TYPE_ERROR_FIX));
                break;
        }
    }

    /**
     * Will check key existence;
     *
     * This method will check the existence of a key, before information can be retrieved from that key. If everything is OK, it
     * will return a boolean true, which means you could go further and process the data. Else, it will return false;
     *
     * @param S $objKey The key to retrieve;
     * @return boolean Will return TRUE if the key was set;
     */
    public function checkKey (S $objKey) {
        switch ($this->objObjectCookie->getConfigKey (new S ('authentication_cookie_store_type'))) {
            case 'cookie':
                // Do return ...
                return new B (isset ($_COOKIE[$this->objProjectString . _U .
                $this->objObjectCookie->getObjectCLASS () . _U . $objKey]) && !empty ($_COOKIE[$this->objProjectString . _U .
                $this->objObjectCookie->getObjectCLASS () . _U . $objKey]));
                break;

            case 'session':
                // Do return ...
                return new B (isset ($_SESSION[$this->objProjectString->toString ()]
                [$this->objObjectCookie->getObjectCLASS ()->toString ()][$objKey->toString ()]) && !empty ($_SESSION[$this
                ->objProjectString->toString ()][$this->objObjectCookie->getObjectCLASS ()->toString ()][$objKey->toString ()]));
                break;

            default:
                // Make an error on it, cause yeah ...
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (SESSION_TYPE_ERROR),
                new S (SESSION_TYPE_ERROR_FIX));
                break;
        }
    }

    /**
     * Will unset the given key;
     *
     * This method, after we are done with our information will clear the cookie information stored at that key. This for example,
     * in the authentication module will deauthenticate the user. Other uses can be thought of in other modules;s
     *
     * @param S $objKey The key to unset;
     * @return boolean Will return true if it was able to unset the key;
     */
    public function unSetKey (S $objKey) {
        switch ($this->objObjectCookie->getConfigKey (new S ('authentication_cookie_store_type'))) {
            case 'cookie':
                // Trigger the browser cookie cleaning mechanism;
                $expTme = new I ($_SERVER['REQUEST_TIME'] - 31556926);

                // Re-set the cookie;
                setcookie ($this->objProjectString . _U . $this->objObjectCookie->getObjectCLASS ()
                . _U . $objKey, new S, $expTme->toInt (), new S ('/'));
                break;

            case 'session':
                // Do unset ...
                unset ($_SESSION[$this->objProjectString->toString ()]
                [$this->objObjectCookie->getObjectCLASS ()->toString ()][$objKey->toString ()]);
                break;

            default:
                // Make an error on it, cause yeah ...
                self::renderScreenOfDeath (new S (__CLASS__),
                new S (SESSION_TYPE_ERROR),
                new S (SESSION_TYPE_ERROR_FIX));
                break;
        }

        // Do return ...
        return new B (TRUE);
    }
}

class MAIL implements IFaceMAIL {
    // Define them statics;
    private static $objHASHId                   = NULL;
    private static $objFromHeader               = NULL;
    private static $objTypeHTML                 = NULL;
    private static $objReplyTo                  = NULL;
    private static $objReceiptTo                = NULL;
    private static $objMIMEHeader               = NULL;
    private static $objEMLAttachment            = NULL;

    // Define them constants ...
    const MAIL_HEADER_PHP_ALTERNATIVE           = '--PHP-alt-%s';
    const MAIL_HEADER_PHP_ALTERNATIVE_END       = '--PHP-alt-%s--';
    const MAIL_HEADER_PHP_MIXED                 = '--PHP-mixed-%s';
    const MAIL_HEADER_PHP_MIXED_END             = '--PHP-mixed-%s--';
    const MAIL_HASH_PREFIX                      = '-RA-PHP-Framework-%s';
    const MAIL_HEADER_MIMEONEZERO               = 'MIME-Version: 1.0';
    const MAIL_HEADER_FROM                      = 'From: %s';
    const MAIL_HEADER_REPLY_TO                  = 'Reply-To: %s';
    const MAIL_HEADER_RETURN_RECEIPT_TO         = 'Return-Receipt-To: %s';
    const MAIL_HEADER_NOTIFICATION_TO           = 'Disposition-Notification-To: %s';
    const MAIL_HEADER_ATTACHMENT_TYPE_NAME      = 'Content-Type: %t; name="%n"';
    const MAIL_HEADER_CONTENT_ENCODING_SEVENBIT = 'Content-Transfer-Encoding: 7bit';
    const MAIL_HEADER_DISPOSITION_ATTACHMENT    = 'Content-Disposition: attachment';
    const MAIL_HEADER_ENCODING_BASE64           = 'Content-Transfer-Encoding: base64';
    const MAIL_HEADER_CONTENT_TYPE_HTML         = 'Content-Type: text/html;  charset="utf-8"';
    const MAIL_HEADER_CONTENT_TYPE_PLAIN        = 'Content-Type: text/plain; charset="utf-8"';
    const MAIL_HEADER_MULTIPART_ALTERNATIVE     = 'Content-Type: multipart/alternative; boundary="PHP-alt-%s"';
    const MAIL_HEADER_MULTIPART_MIXED           = 'Content-type: multipart/mixed; boundary="PHP-mixed-%s"; charset=utf-8';

    // CONSTRUCT;
    public function __construct () {
        // Empty the EML attachements ...
        self::$objEMLAttachment = new A;

        // Set the From, MIME, and Content-type keys;
        self::$objHASHId = _S (self::MAIL_HASH_PREFIX)->doToken ('%s', EXE::getUniqueCode ());
        self::$objTypeHTML = _S (self::MAIL_HEADER_MULTIPART_MIXED . _N_)->doToken ('%s', self::$objHASHId);
        self::$objFromHeader = _S (self::MAIL_HEADER_FROM . _N_)->doToken ('%s', MAIL_FROM);
        self::$objReplyTo = _S (self::MAIL_HEADER_REPLY_TO . _N_)->doToken ('%s', MAIL_FROM);
        self::$objMIMEHeader = _S (self::MAIL_HEADER_MIMEONEZERO . _N_);
    }

    /**
     * Will set the 'From:', e-mail HEADER;
     *
     * This method will set the necessary 'From: ' header on the MIME email format, thus allowing e-mail origination from this
     * website to be properly identified on the .net. In theory, this SHOULD actually be a REAL email, because people tend to
     * push the 'Reply' button quite often, even if they wanna swear you for filling up their Inboxes;
     *
     * @param S $objFromMAIL The mail to set the From: key to;
     */
    public function setFrom (S $objFromMAIL) {
        self::$objFromHeader = _S (self::MAIL_HEADER_FROM . _N_)
        ->doToken ('%s', $objFromMAIL);
    }

    /**
     * Will set the 'Reply-To:', e-mail HEADER, which will allow all outoing emails to be replyed to only ONE address, thing that
     * should help if we ever use PHP to send-out newsletters ...
     *
     * @param S $objReplyTO The email to reply to ...
     */
    public function setReplyTo (S $objReplyTo) {
        self::$objReplyTo = _S (self::MAIL_HEADER_REPLY_TO . _N_)
        ->doToken ('%s', $objReplyTo);
    }

    /**
     * Will return the 'Return-Receipt-To:', e-mail HEADER, which will allow sent emails to be returned to a specific email,
     * which can be checked to be sure that users that have signed-up with our mechanisms are still alive, and working ...
     *
     * @param S $objReceiptTo
     */
    public function setReadReceipt (S $objReceiptTo) {
        self::$objReceiptTo = _S (self::MAIL_HEADER_RETURN_RECEIPT_TO . _N_ .
        self::MAIL_HEADER_NOTIFICATION_TO . _N_)->doToken ('%s', $objReceiptTo);
    }

    /**
     * Will set and attachment to the email to be sent ...
     *
     * This method, given a FilePath to a file will set that file and its name, as the attachment for the email to be sent. These
     * and other constants need to be unset after we use the 'doMAIL', method ...
     *
     * @param FilePath $objEMLAttachment The path to the file we need to attach ...
     * @param S $objEMLContentType We allow the user to give us the content type, not to auto-detect it ...
     */
    public function setAttachment (FilePath $objEMLAttachment, S $objEMLContentType) {
        self::$objEMLAttachment[] = new A (Array ('attachment_name' => basename ($objEMLAttachment),
        'attachment_base64' => chunk_split (base64_encode (file_get_contents ($objEMLAttachment))),
        'attachment_type' => $objEMLContentType));
    }

    /**
     * Will set and attachment to the email to be sent ...
     *
     * This method, given a string to set that file and its name, as the attachment for the email to be sent. These
     * and other constants need to be unset after we use the 'doMAIL', method ...
     *
     * @param S $objEMLAttachment The path to the file we need to attach ...
     * @param S $objEMLContentType We allow the user to give us the content type, not to auto-detect it ...
     */
    public function setStringAttachment (S $objEMLAttachment) {
        self::$objEMLAttachment[] = new A (Array ('attachment_name' => $_SERVER['REQUEST_TIME'] . HTM_EXTENSION,
        'attachment_base64' => chunk_split (base64_encode ($objEMLAttachment)),
        'attachment_type' => HTM_MIME_TYPE));
    }

    /**
     * Will send the email, at the specified address, using the specified subject and content;
     *
     * This method will send out the given email and subject, to the given address, while decoding the content from entities; You,
     * as the user, SHOULD ALWAYS remember, that the content gets decoded in this method, thus allowing you to work with your
     * content where you've called it, without modifying it;
     *
     * @param S $objMAILTo The email to send it to;
     * @param S $objMAILSubject The subject to set;
     * @param S $objMAILContent And the content;
     * @todo CLEAN THIS URGENTLY! ...
     */
    public function doMAIL (S $objMAILTo, S $objMAILSubject, S $objMAILContent) {
        // First of ALL, get a CLONE ...
        $objMAILClone = CLONE $objMAILContent;
        $objMAILClone = $objMAILClone->entityDecode (ENT_QUOTES)->stripTags ();

        // #0: proper MULTIPART;
        $objMAILContent->prependString (new S (_N_))
        ->prependString (_S (self::MAIL_HEADER_CONTENT_ENCODING_SEVENBIT . _N_))
        ->prependString (_S (self::MAIL_HEADER_CONTENT_TYPE_HTML . _N_))
        ->prependString (_S (self::MAIL_HEADER_PHP_ALTERNATIVE . _N_)
                         ->doToken ('%s', self::$objHASHId))
                         ->prependString (_S (_N_))

        // #1: ... end PLAIN;
        ->prependString (_S ($objMAILClone . _N_))->prependString (_S (_N_))
        ->prependString (_S (self::MAIL_HEADER_CONTENT_ENCODING_SEVENBIT . _N_))
        ->prependString (_S (self::MAIL_HEADER_CONTENT_TYPE_PLAIN . _N_))
        ->prependString (_S (self::MAIL_HEADER_PHP_ALTERNATIVE . _N_)
                         ->doToken ('%s', self::$objHASHId))
                         ->prependString (_S (_N_))

        // #3: ... end HTML;
        ->prependString (_S (self::MAIL_HEADER_MULTIPART_ALTERNATIVE . _N_)
                         ->doToken ('%s', self::$objHASHId))
        ->prependString (_S (self::MAIL_HEADER_PHP_MIXED . _N_)
                         ->doToken ('%s', self::$objHASHId));

        // #4: ... end PLAIN and HTML ...
        $objMAILContent->appendString (_S (_N_))
        ->appendString (_S (self::MAIL_HEADER_PHP_ALTERNATIVE_END . _N_)
                        ->doToken ('%s', self::$objHASHId));

        // #5: ... attachments;
        foreach (self::$objEMLAttachment as $k => $v) {
            // #5.1
            $objMAILContent->appendString (_S (self::MAIL_HEADER_PHP_MIXED . _N_)
                            ->doToken ('%s', self::$objHASHId))
            ->appendString (_S (self::MAIL_HEADER_ATTACHMENT_TYPE_NAME . _N_)
                            ->doToken ('%t', $v['attachment_type'])
                            ->doToken ('%n', $v['attachment_name']))

            // #5.2
            ->appendString (_S (self::MAIL_HEADER_ENCODING_BASE64 . _N_))
            ->appendString (_S (self::MAIL_HEADER_DISPOSITION_ATTACHMENT . _N_))
            ->appendString (_S (_N_))->appendString ($v['attachment_base64'])
            ->appendString (_S (self::MAIL_HEADER_PHP_MIXED . _N_)
                            ->doToken ('%s', self::$objHASHId));
        }

        // Finish ...
        $objMAILContent->appendString (_S (_N_))
        ->appendString (_S (self::MAIL_HEADER_PHP_MIXED_END)
                            ->doToken ('%s', self::$objHASHId));

        // Go ... everyhing IS ok and life's grand ...
        MAIL ($objMAILTo, $objMAILSubject, $objMAILContent->entityDecode (ENT_QUOTES),
        self::$objFromHeader . self::$objReceiptTo . self::$objReplyTo .
        self::$objMIMEHeader . self::$objTypeHTML);
    }
}
?>
