<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * Manage simple Tree data structure
 * example of Tree is
 *
 *                             'a'
 *                              |
 *    --------------------------------------------------------------
 *    |                 |                 |              |         |  
 *   'b'               'c'               'd'            'e'       'f'
 *    |                 |         /-----/ |                        |
 *  -------------     ---------  /     --------     ------------------------
 *  |           |     |       | /      |      |     |           |          |
 * 'g'         'h'   'i'     'j'      'k'    'l'   'm'         'n'        'o'
 *                            |
 *                  ----------------------
 *                 |          |          |
 *                'p'        'q'        'r'
 *
 *
 *
 * From the above diagram we have
 *   'a'  - root node
 *   'b'  - child node
 *   'g'  - leaf node
 *   'j'  - node with multiple parents 'c' and 'd'
 *
 *
 * All nodes of the tree (including root and leaf node) contain the following properties
 *       Name      - what is the node name ?
 *       Children  - who are it's children
 *       Data      - any other auxillary data
 *
 *
 * Internally all nodes are an array with the following keys
 *      'name' - string 
 *      'children' - array
 *      'data' - array
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_Utils_Tree {

    /**
     * Store the tree information as a string or array
     * @var string|array
     */
    private $tree;


    /**
     * Constructor for the tree.
     *
     * @param string $root 
     * @return CRM_Utils_Tree

     * @access public
     *
     */
    public function __construct($nodeName)
    {
        // create the root node
        $rootNode =& $this->createNode($nodeName);

        // add the root node to the tree
        $this->tree['rootNode'] =& $rootNode;
    }

    /**
     * Find a node that matches the given string
     *
     * @param string      $name       name of the node we are searching for.
     * @param array (ref) $parentNode which parent node should we search in ?
     *
     * @return array(ref) | false node if found else false
     *
     * @access public
     */
    //public function &findNode(&$parentNode, $name)
    public function &findNode($name, &$parentNode="")
    {
        //CRM_Core_Error::le_method();
        //CRM_Core_Error::debug_var('parentNode', $parentNode);
        //CRM_Core_Error::debug_var('name', $name);

        // if no parent node specified, please start from root node
        if(!$parentNode) {
            $parentNode =& $this->tree['rootNode'];
        }

        // first check the nodename of subtree itself
        if ($parentNode['name'] == $name) {
            return $parentNode;
        }

        // no children ? return false
        if ($this->isLeafNode($node)) {
            return false;
        }

        // search children of the subtree
        foreach ($parentNode['children'] as &$childNode) {
            //print_r($childNode);
            //if ($node =& $this->findNode($childNode, $name)) {
            if ($node =& $this->findNode($name, $childNode)) {
                return $node;
            }
        }

        // name does not match subtree or any of the children, negative result
        return false;
    }

    /**
     * Function to check if node is a leaf node.
     * Currently leaf nodes are strings and non-leaf nodes are arrays
     *
     * @param array(ref) $node node which needs to checked
     * @return boolean
     *
     * @access public
     */
    public function isLeafNode(&$node)
    {
        return (count($node['children']) ? true : false);
    }


    /**
     * Create a node
     *
     * @param string $name 
     * @return array (ref)
     *
     * @access public
     */
    public function &createNode($name)
    {
        $node['name'] = $name;
        $node['children'] = array();
        $node['data'] = array();
        
        return $node;
    }


    /**
     * Add node
     *
     * @param string $parentName - name of the parent ?
     * @param array  (ref)       - node to be added
     * @return none
     *
     * @access public
     */
    public function addNode($parentName, &$node)
    {
        CRM_Core_Error::le_method();
        CRM_Core_Error::debug_var('parentName', $parentName);
        //$parentNode =& $this->findNode($this->tree['rootNode'], $parentName);
        $parentNode =& $this->findNode($parentName);
        CRM_Core_Error::debug_var('parentNode', $parentNode);
        $parentNode['children'][] =& $node;
    }

    /**
     * Add Data
     *
     * @param string $parentName - name of the parent ?
     * @param mixed              - data to be added
     * @param string             - key to be used (optional)
     * @return none
     *
     * @access public
     */
    public function addData($parentName, $data)
    {
        //$parentNode =& $this->findNode($this->tree['rootNode'], $parentName);
        $parentNode =& $this->findNode($parentName);
        if ( empty($parentNode['data']['fKey']) )  {
            $parentNode['data']['fKey'] =& $data;
        }
    }

    /**
     * Get Tree
     *
     * @param none
     * @return tree
     *
     * @access public
     */
    public function getTree()
    {
        return $this->tree;
    }


    /**
     * print the tree
     *
     * @param none
     * @return none
     *
     * @access public
     */
    public function display($node=0, $count=0)
    {
        //print_r($this->tree);
        $increment = 5;
        
        if(!$node) {
            echo "\nTree Output\n";
            $node = $this->tree['rootNode'];
            //$count++;
        }
        //echo $this->tree['rootNode']['name'];
        //string str_pad ( string input, int pad_length [, string pad_string [, int pad_type]])
        $indent = str_pad("", $count*$increment, " ", STR_PAD_LEFT);
        echo $indent;
        echo $node['name'] . "\n";        
        $count++;
        //$count = $count * 5;
        foreach ($node['children'] as $k => $v) {
            $this->display($v, $count);
        }
    }
}

?>
