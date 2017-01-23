<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Driver
 *
 * @author Scott Morken <scott.morken@pcmail.maricopa.edu>
 */

namespace Realestate\MssqlBundle\Driver\PDODblib;
use Realestate\MssqlBundle\Platforms\DblibPlatform;
use Realestate\MssqlBundle\Schema\DblibSchemaManager;

class Driver implements \Doctrine\DBAL\Driver
{
    /**
     * Attempts to establish a connection with the underlying driver.
     *
     * @param array $params
     * @param string $username
     * @param string $password
     * @param array $driverOptions
     * @return Doctrine\DBAL\Driver\Connection
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        if (stristr(PHP_OS, 'WIN')) {
            $conn = new \Doctrine\DBAL\Driver\PDOConnection(
                $this->_constructPdoDsn($params),
                $username,
                $password,
                $driverOptions
            );
        } else {
            $conn = new Connection(
                $this->_constructPdoDsn($params),
                $username,
                $password,
                $driverOptions
            );
        }

        return $conn;
    }

    /**
     * Constructs the Dblib PDO DSN.
     *
     * @return string  The DSN.
     */
    private function _constructPdoDsn(array $params)
    {
        if (stristr(PHP_OS, 'WIN') && PHP_OS != 'Darwin')
        {
            // use for testing on Win
            $dsn = 'sqlsrv:server=';

            if (isset($params['host'])) {
                $dsn .= $params['host'];
            }

            if (isset($params['port']) && !empty($params['port'])) {
                $dsn .= ',' . $params['port'];
            }

            if (isset($params['dbname'])) {
                $dsn .= ';Database=' .  $params['dbname'];
            }
            return $dsn;

        } else {
            $dsn = 'odbc:';
            if (isset($params['host'])) {
                $dsn .= 'Server=' . $params['host'] . ';';
            }
            if (isset($params['port'])) {
                $dsn .= 'Port=' . $params['port'] . ';';
            }
            if (isset($params['dbname'])) {
                $dsn .= 'Database=' . $params['dbname'] . ';';
            }
            /*if (isset($params['charset'])) {
                $dsn .= 'charset=' . $params['charset'] . ';';
            }*/
            if (isset($params['driverOptions']['driver'])) {
                $dsn .= 'Driver=' . $params['driverOptions']['driver'] . ';';
            }

            return $dsn;
        }
    }

    public function getDatabasePlatform()
    {
        return new DblibPlatform();
    }

    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        return new DblibSchemaManager($conn);
    }

    public function getName()
    {
        return 'pdo_odbc';
    }

    public function getDatabase(\Doctrine\DBAL\Connection $conn)
    {
        $params = $conn->getParams();
        return $params['dbname'];
    }
}
