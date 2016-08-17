<?php

namespace Dende\SoccerBot\Model\Base;

use \Exception;
use \PDO;
use Dende\SoccerBot\Model\Match as ChildMatch;
use Dende\SoccerBot\Model\MatchQuery as ChildMatchQuery;
use Dende\SoccerBot\Model\Map\MatchTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'matches' table.
 *
 *
 *
 * @method     ChildMatchQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildMatchQuery orderByHomeTeamId($order = Criteria::ASC) Order by the home_team_id column
 * @method     ChildMatchQuery orderByAwayTeamId($order = Criteria::ASC) Order by the away_team_id column
 * @method     ChildMatchQuery orderByHomeTeamGoals($order = Criteria::ASC) Order by the home_team_goals column
 * @method     ChildMatchQuery orderByAwayTeamGoals($order = Criteria::ASC) Order by the away_team_goals column
 * @method     ChildMatchQuery orderByStatus($order = Criteria::ASC) Order by the status column
 * @method     ChildMatchQuery orderByDate($order = Criteria::ASC) Order by the date column
 * @method     ChildMatchQuery orderByUrl($order = Criteria::ASC) Order by the url column
 *
 * @method     ChildMatchQuery groupById() Group by the id column
 * @method     ChildMatchQuery groupByHomeTeamId() Group by the home_team_id column
 * @method     ChildMatchQuery groupByAwayTeamId() Group by the away_team_id column
 * @method     ChildMatchQuery groupByHomeTeamGoals() Group by the home_team_goals column
 * @method     ChildMatchQuery groupByAwayTeamGoals() Group by the away_team_goals column
 * @method     ChildMatchQuery groupByStatus() Group by the status column
 * @method     ChildMatchQuery groupByDate() Group by the date column
 * @method     ChildMatchQuery groupByUrl() Group by the url column
 *
 * @method     ChildMatchQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildMatchQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildMatchQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildMatchQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildMatchQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildMatchQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildMatchQuery leftJoinHomeTeam($relationAlias = null) Adds a LEFT JOIN clause to the query using the HomeTeam relation
 * @method     ChildMatchQuery rightJoinHomeTeam($relationAlias = null) Adds a RIGHT JOIN clause to the query using the HomeTeam relation
 * @method     ChildMatchQuery innerJoinHomeTeam($relationAlias = null) Adds a INNER JOIN clause to the query using the HomeTeam relation
 *
 * @method     ChildMatchQuery joinWithHomeTeam($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the HomeTeam relation
 *
 * @method     ChildMatchQuery leftJoinWithHomeTeam() Adds a LEFT JOIN clause and with to the query using the HomeTeam relation
 * @method     ChildMatchQuery rightJoinWithHomeTeam() Adds a RIGHT JOIN clause and with to the query using the HomeTeam relation
 * @method     ChildMatchQuery innerJoinWithHomeTeam() Adds a INNER JOIN clause and with to the query using the HomeTeam relation
 *
 * @method     ChildMatchQuery leftJoinAwayTeam($relationAlias = null) Adds a LEFT JOIN clause to the query using the AwayTeam relation
 * @method     ChildMatchQuery rightJoinAwayTeam($relationAlias = null) Adds a RIGHT JOIN clause to the query using the AwayTeam relation
 * @method     ChildMatchQuery innerJoinAwayTeam($relationAlias = null) Adds a INNER JOIN clause to the query using the AwayTeam relation
 *
 * @method     ChildMatchQuery joinWithAwayTeam($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the AwayTeam relation
 *
 * @method     ChildMatchQuery leftJoinWithAwayTeam() Adds a LEFT JOIN clause and with to the query using the AwayTeam relation
 * @method     ChildMatchQuery rightJoinWithAwayTeam() Adds a RIGHT JOIN clause and with to the query using the AwayTeam relation
 * @method     ChildMatchQuery innerJoinWithAwayTeam() Adds a INNER JOIN clause and with to the query using the AwayTeam relation
 *
 * @method     ChildMatchQuery leftJoinPrivateChat($relationAlias = null) Adds a LEFT JOIN clause to the query using the PrivateChat relation
 * @method     ChildMatchQuery rightJoinPrivateChat($relationAlias = null) Adds a RIGHT JOIN clause to the query using the PrivateChat relation
 * @method     ChildMatchQuery innerJoinPrivateChat($relationAlias = null) Adds a INNER JOIN clause to the query using the PrivateChat relation
 *
 * @method     ChildMatchQuery joinWithPrivateChat($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the PrivateChat relation
 *
 * @method     ChildMatchQuery leftJoinWithPrivateChat() Adds a LEFT JOIN clause and with to the query using the PrivateChat relation
 * @method     ChildMatchQuery rightJoinWithPrivateChat() Adds a RIGHT JOIN clause and with to the query using the PrivateChat relation
 * @method     ChildMatchQuery innerJoinWithPrivateChat() Adds a INNER JOIN clause and with to the query using the PrivateChat relation
 *
 * @method     ChildMatchQuery leftJoinBet($relationAlias = null) Adds a LEFT JOIN clause to the query using the Bet relation
 * @method     ChildMatchQuery rightJoinBet($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Bet relation
 * @method     ChildMatchQuery innerJoinBet($relationAlias = null) Adds a INNER JOIN clause to the query using the Bet relation
 *
 * @method     ChildMatchQuery joinWithBet($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Bet relation
 *
 * @method     ChildMatchQuery leftJoinWithBet() Adds a LEFT JOIN clause and with to the query using the Bet relation
 * @method     ChildMatchQuery rightJoinWithBet() Adds a RIGHT JOIN clause and with to the query using the Bet relation
 * @method     ChildMatchQuery innerJoinWithBet() Adds a INNER JOIN clause and with to the query using the Bet relation
 *
 * @method     \Dende\SoccerBot\Model\TeamQuery|\Dende\SoccerBot\Model\PrivateChatQuery|\Dende\SoccerBot\Model\BetQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildMatch findOne(ConnectionInterface $con = null) Return the first ChildMatch matching the query
 * @method     ChildMatch findOneOrCreate(ConnectionInterface $con = null) Return the first ChildMatch matching the query, or a new ChildMatch object populated from the query conditions when no match is found
 *
 * @method     ChildMatch findOneById(int $id) Return the first ChildMatch filtered by the id column
 * @method     ChildMatch findOneByHomeTeamId(int $home_team_id) Return the first ChildMatch filtered by the home_team_id column
 * @method     ChildMatch findOneByAwayTeamId(int $away_team_id) Return the first ChildMatch filtered by the away_team_id column
 * @method     ChildMatch findOneByHomeTeamGoals(int $home_team_goals) Return the first ChildMatch filtered by the home_team_goals column
 * @method     ChildMatch findOneByAwayTeamGoals(int $away_team_goals) Return the first ChildMatch filtered by the away_team_goals column
 * @method     ChildMatch findOneByStatus(string $status) Return the first ChildMatch filtered by the status column
 * @method     ChildMatch findOneByDate(string $date) Return the first ChildMatch filtered by the date column
 * @method     ChildMatch findOneByUrl(string $url) Return the first ChildMatch filtered by the url column *

 * @method     ChildMatch requirePk($key, ConnectionInterface $con = null) Return the ChildMatch by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMatch requireOne(ConnectionInterface $con = null) Return the first ChildMatch matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildMatch requireOneById(int $id) Return the first ChildMatch filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMatch requireOneByHomeTeamId(int $home_team_id) Return the first ChildMatch filtered by the home_team_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMatch requireOneByAwayTeamId(int $away_team_id) Return the first ChildMatch filtered by the away_team_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMatch requireOneByHomeTeamGoals(int $home_team_goals) Return the first ChildMatch filtered by the home_team_goals column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMatch requireOneByAwayTeamGoals(int $away_team_goals) Return the first ChildMatch filtered by the away_team_goals column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMatch requireOneByStatus(string $status) Return the first ChildMatch filtered by the status column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMatch requireOneByDate(string $date) Return the first ChildMatch filtered by the date column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildMatch requireOneByUrl(string $url) Return the first ChildMatch filtered by the url column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildMatch[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildMatch objects based on current ModelCriteria
 * @method     ChildMatch[]|ObjectCollection findById(int $id) Return ChildMatch objects filtered by the id column
 * @method     ChildMatch[]|ObjectCollection findByHomeTeamId(int $home_team_id) Return ChildMatch objects filtered by the home_team_id column
 * @method     ChildMatch[]|ObjectCollection findByAwayTeamId(int $away_team_id) Return ChildMatch objects filtered by the away_team_id column
 * @method     ChildMatch[]|ObjectCollection findByHomeTeamGoals(int $home_team_goals) Return ChildMatch objects filtered by the home_team_goals column
 * @method     ChildMatch[]|ObjectCollection findByAwayTeamGoals(int $away_team_goals) Return ChildMatch objects filtered by the away_team_goals column
 * @method     ChildMatch[]|ObjectCollection findByStatus(string $status) Return ChildMatch objects filtered by the status column
 * @method     ChildMatch[]|ObjectCollection findByDate(string $date) Return ChildMatch objects filtered by the date column
 * @method     ChildMatch[]|ObjectCollection findByUrl(string $url) Return ChildMatch objects filtered by the url column
 * @method     ChildMatch[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class MatchQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Dende\SoccerBot\Model\Base\MatchQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'default', $modelName = '\\Dende\\SoccerBot\\Model\\Match', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildMatchQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildMatchQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildMatchQuery) {
            return $criteria;
        }
        $query = new ChildMatchQuery();
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
     * @return ChildMatch|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(MatchTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = MatchTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @return ChildMatch A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, home_team_id, away_team_id, home_team_goals, away_team_goals, status, date, url FROM matches WHERE id = :p0';
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
            /** @var ChildMatch $obj */
            $obj = new ChildMatch();
            $obj->hydrate($row);
            MatchTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildMatch|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(MatchTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(MatchTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(MatchTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(MatchTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MatchTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the home_team_id column
     *
     * Example usage:
     * <code>
     * $query->filterByHomeTeamId(1234); // WHERE home_team_id = 1234
     * $query->filterByHomeTeamId(array(12, 34)); // WHERE home_team_id IN (12, 34)
     * $query->filterByHomeTeamId(array('min' => 12)); // WHERE home_team_id > 12
     * </code>
     *
     * @see       filterByHomeTeam()
     *
     * @param     mixed $homeTeamId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByHomeTeamId($homeTeamId = null, $comparison = null)
    {
        if (is_array($homeTeamId)) {
            $useMinMax = false;
            if (isset($homeTeamId['min'])) {
                $this->addUsingAlias(MatchTableMap::COL_HOME_TEAM_ID, $homeTeamId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($homeTeamId['max'])) {
                $this->addUsingAlias(MatchTableMap::COL_HOME_TEAM_ID, $homeTeamId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MatchTableMap::COL_HOME_TEAM_ID, $homeTeamId, $comparison);
    }

    /**
     * Filter the query on the away_team_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAwayTeamId(1234); // WHERE away_team_id = 1234
     * $query->filterByAwayTeamId(array(12, 34)); // WHERE away_team_id IN (12, 34)
     * $query->filterByAwayTeamId(array('min' => 12)); // WHERE away_team_id > 12
     * </code>
     *
     * @see       filterByAwayTeam()
     *
     * @param     mixed $awayTeamId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByAwayTeamId($awayTeamId = null, $comparison = null)
    {
        if (is_array($awayTeamId)) {
            $useMinMax = false;
            if (isset($awayTeamId['min'])) {
                $this->addUsingAlias(MatchTableMap::COL_AWAY_TEAM_ID, $awayTeamId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($awayTeamId['max'])) {
                $this->addUsingAlias(MatchTableMap::COL_AWAY_TEAM_ID, $awayTeamId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MatchTableMap::COL_AWAY_TEAM_ID, $awayTeamId, $comparison);
    }

    /**
     * Filter the query on the home_team_goals column
     *
     * Example usage:
     * <code>
     * $query->filterByHomeTeamGoals(1234); // WHERE home_team_goals = 1234
     * $query->filterByHomeTeamGoals(array(12, 34)); // WHERE home_team_goals IN (12, 34)
     * $query->filterByHomeTeamGoals(array('min' => 12)); // WHERE home_team_goals > 12
     * </code>
     *
     * @param     mixed $homeTeamGoals The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByHomeTeamGoals($homeTeamGoals = null, $comparison = null)
    {
        if (is_array($homeTeamGoals)) {
            $useMinMax = false;
            if (isset($homeTeamGoals['min'])) {
                $this->addUsingAlias(MatchTableMap::COL_HOME_TEAM_GOALS, $homeTeamGoals['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($homeTeamGoals['max'])) {
                $this->addUsingAlias(MatchTableMap::COL_HOME_TEAM_GOALS, $homeTeamGoals['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MatchTableMap::COL_HOME_TEAM_GOALS, $homeTeamGoals, $comparison);
    }

    /**
     * Filter the query on the away_team_goals column
     *
     * Example usage:
     * <code>
     * $query->filterByAwayTeamGoals(1234); // WHERE away_team_goals = 1234
     * $query->filterByAwayTeamGoals(array(12, 34)); // WHERE away_team_goals IN (12, 34)
     * $query->filterByAwayTeamGoals(array('min' => 12)); // WHERE away_team_goals > 12
     * </code>
     *
     * @param     mixed $awayTeamGoals The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByAwayTeamGoals($awayTeamGoals = null, $comparison = null)
    {
        if (is_array($awayTeamGoals)) {
            $useMinMax = false;
            if (isset($awayTeamGoals['min'])) {
                $this->addUsingAlias(MatchTableMap::COL_AWAY_TEAM_GOALS, $awayTeamGoals['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($awayTeamGoals['max'])) {
                $this->addUsingAlias(MatchTableMap::COL_AWAY_TEAM_GOALS, $awayTeamGoals['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MatchTableMap::COL_AWAY_TEAM_GOALS, $awayTeamGoals, $comparison);
    }

    /**
     * Filter the query on the status column
     *
     * Example usage:
     * <code>
     * $query->filterByStatus('fooValue');   // WHERE status = 'fooValue'
     * $query->filterByStatus('%fooValue%'); // WHERE status LIKE '%fooValue%'
     * </code>
     *
     * @param     string $status The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByStatus($status = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($status)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MatchTableMap::COL_STATUS, $status, $comparison);
    }

    /**
     * Filter the query on the date column
     *
     * Example usage:
     * <code>
     * $query->filterByDate('2011-03-14'); // WHERE date = '2011-03-14'
     * $query->filterByDate('now'); // WHERE date = '2011-03-14'
     * $query->filterByDate(array('max' => 'yesterday')); // WHERE date > '2011-03-13'
     * </code>
     *
     * @param     mixed $date The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByDate($date = null, $comparison = null)
    {
        if (is_array($date)) {
            $useMinMax = false;
            if (isset($date['min'])) {
                $this->addUsingAlias(MatchTableMap::COL_DATE, $date['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($date['max'])) {
                $this->addUsingAlias(MatchTableMap::COL_DATE, $date['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MatchTableMap::COL_DATE, $date, $comparison);
    }

    /**
     * Filter the query on the url column
     *
     * Example usage:
     * <code>
     * $query->filterByUrl('fooValue');   // WHERE url = 'fooValue'
     * $query->filterByUrl('%fooValue%'); // WHERE url LIKE '%fooValue%'
     * </code>
     *
     * @param     string $url The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function filterByUrl($url = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($url)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(MatchTableMap::COL_URL, $url, $comparison);
    }

    /**
     * Filter the query by a related \Dende\SoccerBot\Model\Team object
     *
     * @param \Dende\SoccerBot\Model\Team|ObjectCollection $team The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildMatchQuery The current query, for fluid interface
     */
    public function filterByHomeTeam($team, $comparison = null)
    {
        if ($team instanceof \Dende\SoccerBot\Model\Team) {
            return $this
                ->addUsingAlias(MatchTableMap::COL_HOME_TEAM_ID, $team->getId(), $comparison);
        } elseif ($team instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MatchTableMap::COL_HOME_TEAM_ID, $team->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByHomeTeam() only accepts arguments of type \Dende\SoccerBot\Model\Team or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the HomeTeam relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function joinHomeTeam($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('HomeTeam');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'HomeTeam');
        }

        return $this;
    }

    /**
     * Use the HomeTeam relation Team object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Dende\SoccerBot\Model\TeamQuery A secondary query class using the current class as primary query
     */
    public function useHomeTeamQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinHomeTeam($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'HomeTeam', '\Dende\SoccerBot\Model\TeamQuery');
    }

    /**
     * Filter the query by a related \Dende\SoccerBot\Model\Team object
     *
     * @param \Dende\SoccerBot\Model\Team|ObjectCollection $team The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildMatchQuery The current query, for fluid interface
     */
    public function filterByAwayTeam($team, $comparison = null)
    {
        if ($team instanceof \Dende\SoccerBot\Model\Team) {
            return $this
                ->addUsingAlias(MatchTableMap::COL_AWAY_TEAM_ID, $team->getId(), $comparison);
        } elseif ($team instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(MatchTableMap::COL_AWAY_TEAM_ID, $team->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByAwayTeam() only accepts arguments of type \Dende\SoccerBot\Model\Team or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the AwayTeam relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function joinAwayTeam($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('AwayTeam');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'AwayTeam');
        }

        return $this;
    }

    /**
     * Use the AwayTeam relation Team object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Dende\SoccerBot\Model\TeamQuery A secondary query class using the current class as primary query
     */
    public function useAwayTeamQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAwayTeam($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'AwayTeam', '\Dende\SoccerBot\Model\TeamQuery');
    }

    /**
     * Filter the query by a related \Dende\SoccerBot\Model\PrivateChat object
     *
     * @param \Dende\SoccerBot\Model\PrivateChat|ObjectCollection $privateChat the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMatchQuery The current query, for fluid interface
     */
    public function filterByPrivateChat($privateChat, $comparison = null)
    {
        if ($privateChat instanceof \Dende\SoccerBot\Model\PrivateChat) {
            return $this
                ->addUsingAlias(MatchTableMap::COL_ID, $privateChat->getCurrentBetMatchId(), $comparison);
        } elseif ($privateChat instanceof ObjectCollection) {
            return $this
                ->usePrivateChatQuery()
                ->filterByPrimaryKeys($privateChat->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByPrivateChat() only accepts arguments of type \Dende\SoccerBot\Model\PrivateChat or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the PrivateChat relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function joinPrivateChat($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('PrivateChat');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'PrivateChat');
        }

        return $this;
    }

    /**
     * Use the PrivateChat relation PrivateChat object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Dende\SoccerBot\Model\PrivateChatQuery A secondary query class using the current class as primary query
     */
    public function usePrivateChatQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPrivateChat($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'PrivateChat', '\Dende\SoccerBot\Model\PrivateChatQuery');
    }

    /**
     * Filter the query by a related \Dende\SoccerBot\Model\Bet object
     *
     * @param \Dende\SoccerBot\Model\Bet|ObjectCollection $bet the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildMatchQuery The current query, for fluid interface
     */
    public function filterByBet($bet, $comparison = null)
    {
        if ($bet instanceof \Dende\SoccerBot\Model\Bet) {
            return $this
                ->addUsingAlias(MatchTableMap::COL_ID, $bet->getMatchId(), $comparison);
        } elseif ($bet instanceof ObjectCollection) {
            return $this
                ->useBetQuery()
                ->filterByPrimaryKeys($bet->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByBet() only accepts arguments of type \Dende\SoccerBot\Model\Bet or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Bet relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function joinBet($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Bet');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Bet');
        }

        return $this;
    }

    /**
     * Use the Bet relation Bet object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Dende\SoccerBot\Model\BetQuery A secondary query class using the current class as primary query
     */
    public function useBetQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinBet($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Bet', '\Dende\SoccerBot\Model\BetQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildMatch $match Object to remove from the list of results
     *
     * @return $this|ChildMatchQuery The current query, for fluid interface
     */
    public function prune($match = null)
    {
        if ($match) {
            $this->addUsingAlias(MatchTableMap::COL_ID, $match->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the matches table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(MatchTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            MatchTableMap::clearInstancePool();
            MatchTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(MatchTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(MatchTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            MatchTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            MatchTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // MatchQuery
