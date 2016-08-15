<?php

namespace Dende\SoccerBot\Model\Base;

use \Exception;
use \PDO;
use Dende\SoccerBot\Model\PrivateChat as ChildPrivateChat;
use Dende\SoccerBot\Model\PrivateChatQuery as ChildPrivateChatQuery;
use Dende\SoccerBot\Model\Map\PrivateChatTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'privatechats' table.
 *
 *
 *
 * @method     ChildPrivateChatQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildPrivateChatQuery orderByChatId($order = Criteria::ASC) Order by the chat_id column
 * @method     ChildPrivateChatQuery orderByType($order = Criteria::ASC) Order by the type column
 * @method     ChildPrivateChatQuery orderByLiveticker($order = Criteria::ASC) Order by the liveticker column
 * @method     ChildPrivateChatQuery orderByRegisterstatus($order = Criteria::ASC) Order by the registerstatus column
 *
 * @method     ChildPrivateChatQuery groupById() Group by the id column
 * @method     ChildPrivateChatQuery groupByChatId() Group by the chat_id column
 * @method     ChildPrivateChatQuery groupByType() Group by the type column
 * @method     ChildPrivateChatQuery groupByLiveticker() Group by the liveticker column
 * @method     ChildPrivateChatQuery groupByRegisterstatus() Group by the registerstatus column
 *
 * @method     ChildPrivateChatQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildPrivateChatQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildPrivateChatQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildPrivateChatQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildPrivateChatQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildPrivateChatQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildPrivateChat findOne(ConnectionInterface $con = null) Return the first ChildPrivateChat matching the query
 * @method     ChildPrivateChat findOneOrCreate(ConnectionInterface $con = null) Return the first ChildPrivateChat matching the query, or a new ChildPrivateChat object populated from the query conditions when no match is found
 *
 * @method     ChildPrivateChat findOneById(int $id) Return the first ChildPrivateChat filtered by the id column
 * @method     ChildPrivateChat findOneByChatId(int $chat_id) Return the first ChildPrivateChat filtered by the chat_id column
 * @method     ChildPrivateChat findOneByType(string $type) Return the first ChildPrivateChat filtered by the type column
 * @method     ChildPrivateChat findOneByLiveticker(boolean $liveticker) Return the first ChildPrivateChat filtered by the liveticker column
 * @method     ChildPrivateChat findOneByRegisterstatus(string $registerstatus) Return the first ChildPrivateChat filtered by the registerstatus column *

 * @method     ChildPrivateChat requirePk($key, ConnectionInterface $con = null) Return the ChildPrivateChat by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPrivateChat requireOne(ConnectionInterface $con = null) Return the first ChildPrivateChat matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildPrivateChat requireOneById(int $id) Return the first ChildPrivateChat filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPrivateChat requireOneByChatId(int $chat_id) Return the first ChildPrivateChat filtered by the chat_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPrivateChat requireOneByType(string $type) Return the first ChildPrivateChat filtered by the type column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPrivateChat requireOneByLiveticker(boolean $liveticker) Return the first ChildPrivateChat filtered by the liveticker column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPrivateChat requireOneByRegisterstatus(string $registerstatus) Return the first ChildPrivateChat filtered by the registerstatus column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildPrivateChat[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildPrivateChat objects based on current ModelCriteria
 * @method     ChildPrivateChat[]|ObjectCollection findById(int $id) Return ChildPrivateChat objects filtered by the id column
 * @method     ChildPrivateChat[]|ObjectCollection findByChatId(int $chat_id) Return ChildPrivateChat objects filtered by the chat_id column
 * @method     ChildPrivateChat[]|ObjectCollection findByType(string $type) Return ChildPrivateChat objects filtered by the type column
 * @method     ChildPrivateChat[]|ObjectCollection findByLiveticker(boolean $liveticker) Return ChildPrivateChat objects filtered by the liveticker column
 * @method     ChildPrivateChat[]|ObjectCollection findByRegisterstatus(string $registerstatus) Return ChildPrivateChat objects filtered by the registerstatus column
 * @method     ChildPrivateChat[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class PrivateChatQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Dende\SoccerBot\Model\Base\PrivateChatQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Dende\\SoccerBot\\Model\\PrivateChat', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildPrivateChatQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildPrivateChatQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildPrivateChatQuery) {
            return $criteria;
        }
        $query = new ChildPrivateChatQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildPrivateChat|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(PrivateChatTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = PrivateChatTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildPrivateChat A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, chat_id, type, liveticker, registerstatus FROM privatechats WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildPrivateChat $obj */
            $obj = new ChildPrivateChat();
            $obj->hydrate($row);
            PrivateChatTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildPrivateChat|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildPrivateChatQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(PrivateChatTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildPrivateChatQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(PrivateChatTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPrivateChatQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(PrivateChatTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PrivateChatTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PrivateChatTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the chat_id column
     *
     * Example usage:
     * <code>
     * $query->filterByChatId(1234); // WHERE chat_id = 1234
     * $query->filterByChatId(array(12, 34)); // WHERE chat_id IN (12, 34)
     * $query->filterByChatId(array('min' => 12)); // WHERE chat_id > 12
     * </code>
     *
     * @param     mixed $chatId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPrivateChatQuery The current query, for fluid interface
     */
    public function filterByChatId($chatId = null, $comparison = null)
    {
        if (is_array($chatId)) {
            $useMinMax = false;
            if (isset($chatId['min'])) {
                $this->addUsingAlias(PrivateChatTableMap::COL_CHAT_ID, $chatId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($chatId['max'])) {
                $this->addUsingAlias(PrivateChatTableMap::COL_CHAT_ID, $chatId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PrivateChatTableMap::COL_CHAT_ID, $chatId, $comparison);
    }

    /**
     * Filter the query on the type column
     *
     * Example usage:
     * <code>
     * $query->filterByType('fooValue');   // WHERE type = 'fooValue'
     * $query->filterByType('%fooValue%'); // WHERE type LIKE '%fooValue%'
     * </code>
     *
     * @param     string $type The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPrivateChatQuery The current query, for fluid interface
     */
    public function filterByType($type = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($type)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PrivateChatTableMap::COL_TYPE, $type, $comparison);
    }

    /**
     * Filter the query on the liveticker column
     *
     * Example usage:
     * <code>
     * $query->filterByLiveticker(true); // WHERE liveticker = true
     * $query->filterByLiveticker('yes'); // WHERE liveticker = true
     * </code>
     *
     * @param     boolean|string $liveticker The value to use as filter.
     *              Non-boolean arguments are converted using the following rules:
     *                * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *                * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     *              Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPrivateChatQuery The current query, for fluid interface
     */
    public function filterByLiveticker($liveticker = null, $comparison = null)
    {
        if (is_string($liveticker)) {
            $liveticker = in_array(strtolower($liveticker), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
        }

        return $this->addUsingAlias(PrivateChatTableMap::COL_LIVETICKER, $liveticker, $comparison);
    }

    /**
     * Filter the query on the registerstatus column
     *
     * Example usage:
     * <code>
     * $query->filterByRegisterstatus('fooValue');   // WHERE registerstatus = 'fooValue'
     * $query->filterByRegisterstatus('%fooValue%'); // WHERE registerstatus LIKE '%fooValue%'
     * </code>
     *
     * @param     string $registerstatus The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildPrivateChatQuery The current query, for fluid interface
     */
    public function filterByRegisterstatus($registerstatus = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($registerstatus)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(PrivateChatTableMap::COL_REGISTERSTATUS, $registerstatus, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param   ChildPrivateChat $privateChat Object to remove from the list of results
     *
     * @return $this|ChildPrivateChatQuery The current query, for fluid interface
     */
    public function prune($privateChat = null)
    {
        if ($privateChat) {
            $this->addUsingAlias(PrivateChatTableMap::COL_ID, $privateChat->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the privatechats table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PrivateChatTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            PrivateChatTableMap::clearInstancePool();
            PrivateChatTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PrivateChatTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(PrivateChatTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            PrivateChatTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            PrivateChatTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // PrivateChatQuery
