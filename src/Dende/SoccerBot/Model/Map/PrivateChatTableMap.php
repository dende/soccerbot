<?php

namespace Dende\SoccerBot\Model\Map;

use Dende\SoccerBot\Model\PrivateChat;
use Dende\SoccerBot\Model\PrivateChatQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'privatechats' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class PrivateChatTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'Dende.SoccerBot.Model.Map.PrivateChatTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'default';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'privatechats';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Dende\\SoccerBot\\Model\\PrivateChat';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Dende.SoccerBot.Model.PrivateChat';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 8;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 8;

    /**
     * the column name for the id field
     */
    const COL_ID = 'privatechats.id';

    /**
     * the column name for the chat_id field
     */
    const COL_CHAT_ID = 'privatechats.chat_id';

    /**
     * the column name for the username field
     */
    const COL_USERNAME = 'privatechats.username';

    /**
     * the column name for the type field
     */
    const COL_TYPE = 'privatechats.type';

    /**
     * the column name for the liveticker field
     */
    const COL_LIVETICKER = 'privatechats.liveticker';

    /**
     * the column name for the registerstatus field
     */
    const COL_REGISTERSTATUS = 'privatechats.registerstatus';

    /**
     * the column name for the betstatus field
     */
    const COL_BETSTATUS = 'privatechats.betstatus';

    /**
     * the column name for the current_bet_match_id field
     */
    const COL_CURRENT_BET_MATCH_ID = 'privatechats.current_bet_match_id';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'ChatId', 'Username', 'Type', 'Liveticker', 'Registerstatus', 'Betstatus', 'CurrentBetMatchId', ),
        self::TYPE_CAMELNAME     => array('id', 'chatId', 'username', 'type', 'liveticker', 'registerstatus', 'betstatus', 'currentBetMatchId', ),
        self::TYPE_COLNAME       => array(PrivateChatTableMap::COL_ID, PrivateChatTableMap::COL_CHAT_ID, PrivateChatTableMap::COL_USERNAME, PrivateChatTableMap::COL_TYPE, PrivateChatTableMap::COL_LIVETICKER, PrivateChatTableMap::COL_REGISTERSTATUS, PrivateChatTableMap::COL_BETSTATUS, PrivateChatTableMap::COL_CURRENT_BET_MATCH_ID, ),
        self::TYPE_FIELDNAME     => array('id', 'chat_id', 'username', 'type', 'liveticker', 'registerstatus', 'betstatus', 'current_bet_match_id', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'ChatId' => 1, 'Username' => 2, 'Type' => 3, 'Liveticker' => 4, 'Registerstatus' => 5, 'Betstatus' => 6, 'CurrentBetMatchId' => 7, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'chatId' => 1, 'username' => 2, 'type' => 3, 'liveticker' => 4, 'registerstatus' => 5, 'betstatus' => 6, 'currentBetMatchId' => 7, ),
        self::TYPE_COLNAME       => array(PrivateChatTableMap::COL_ID => 0, PrivateChatTableMap::COL_CHAT_ID => 1, PrivateChatTableMap::COL_USERNAME => 2, PrivateChatTableMap::COL_TYPE => 3, PrivateChatTableMap::COL_LIVETICKER => 4, PrivateChatTableMap::COL_REGISTERSTATUS => 5, PrivateChatTableMap::COL_BETSTATUS => 6, PrivateChatTableMap::COL_CURRENT_BET_MATCH_ID => 7, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'chat_id' => 1, 'username' => 2, 'type' => 3, 'liveticker' => 4, 'registerstatus' => 5, 'betstatus' => 6, 'current_bet_match_id' => 7, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('privatechats');
        $this->setPhpName('PrivateChat');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\Dende\\SoccerBot\\Model\\PrivateChat');
        $this->setPackage('Dende.SoccerBot.Model');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('chat_id', 'ChatId', 'INTEGER', true, null, null);
        $this->addColumn('username', 'Username', 'VARCHAR', false, 30, null);
        $this->addColumn('type', 'Type', 'VARCHAR', true, 20, null);
        $this->addColumn('liveticker', 'Liveticker', 'BOOLEAN', false, 1, false);
        $this->addColumn('registerstatus', 'Registerstatus', 'VARCHAR', true, 50, 'unregistered');
        $this->addColumn('betstatus', 'Betstatus', 'VARCHAR', true, 50, 'inactive');
        $this->addForeignKey('current_bet_match_id', 'CurrentBetMatchId', 'INTEGER', 'matches', 'id', false, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('CurrentBetMatch', '\\Dende\\SoccerBot\\Model\\Match', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':current_bet_match_id',
    1 => ':id',
  ),
), null, null, null, false);
        $this->addRelation('Bet', '\\Dende\\SoccerBot\\Model\\Bet', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':chat_id',
    1 => ':id',
  ),
), null, null, 'Bets', false);
    } // buildRelations()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? PrivateChatTableMap::CLASS_DEFAULT : PrivateChatTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (PrivateChat object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = PrivateChatTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = PrivateChatTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + PrivateChatTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = PrivateChatTableMap::OM_CLASS;
            /** @var PrivateChat $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            PrivateChatTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = PrivateChatTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = PrivateChatTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var PrivateChat $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                PrivateChatTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(PrivateChatTableMap::COL_ID);
            $criteria->addSelectColumn(PrivateChatTableMap::COL_CHAT_ID);
            $criteria->addSelectColumn(PrivateChatTableMap::COL_USERNAME);
            $criteria->addSelectColumn(PrivateChatTableMap::COL_TYPE);
            $criteria->addSelectColumn(PrivateChatTableMap::COL_LIVETICKER);
            $criteria->addSelectColumn(PrivateChatTableMap::COL_REGISTERSTATUS);
            $criteria->addSelectColumn(PrivateChatTableMap::COL_BETSTATUS);
            $criteria->addSelectColumn(PrivateChatTableMap::COL_CURRENT_BET_MATCH_ID);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.chat_id');
            $criteria->addSelectColumn($alias . '.username');
            $criteria->addSelectColumn($alias . '.type');
            $criteria->addSelectColumn($alias . '.liveticker');
            $criteria->addSelectColumn($alias . '.registerstatus');
            $criteria->addSelectColumn($alias . '.betstatus');
            $criteria->addSelectColumn($alias . '.current_bet_match_id');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(PrivateChatTableMap::DATABASE_NAME)->getTable(PrivateChatTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(PrivateChatTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(PrivateChatTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new PrivateChatTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a PrivateChat or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or PrivateChat object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PrivateChatTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Dende\SoccerBot\Model\PrivateChat) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(PrivateChatTableMap::DATABASE_NAME);
            $criteria->add(PrivateChatTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = PrivateChatQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            PrivateChatTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                PrivateChatTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the privatechats table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return PrivateChatQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a PrivateChat or Criteria object.
     *
     * @param mixed               $criteria Criteria or PrivateChat object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PrivateChatTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from PrivateChat object
        }

        if ($criteria->containsKey(PrivateChatTableMap::COL_ID) && $criteria->keyContainsValue(PrivateChatTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.PrivateChatTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = PrivateChatQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // PrivateChatTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
PrivateChatTableMap::buildTableMap();
