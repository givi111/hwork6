<?php
namespace Database;
use PDO;
use PDOException;
class DataManager
{
    /**
     * Current PDO object.
     *
     * @var PDO $_connection
     */
    private $_connection;
    /**
     * DataManager constructor.
     *
     * @param string $database Database name.
     * @param string $user Database user.
     * @param string $password Database password.
     * @param string $host Database host.
     */
    public function __construct(string $database, string $user,
                                string $password, string $host = 'localhost'
    )
    {
        try {
            $conn = new PDO(
                "mysql:host={$host};dbname={$database}", $user, $password
            );
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_connection = $conn;
        } catch (PDOException $e) {
            echo $e->getCode() . ': ' . $e->getMessage();
        }
    }
    /**
     * Select data current database instance.
     *
     * @param array $columns DB column names.
     * @param string $table DB table name.
     * @param array|null $where Where statement.
     * @param array|null $order_by Order statement.
     * @param int|null $limit Data limit.
     *
     * @return array|null
     */
    public function select(array $columns, string $table, array $where = null,
                           array $order_by = null, int $limit = null
    ): ?array
    {
        // Define base select query.
        $query = 'SELECT `' . implode($columns, '`, `') . '` FROM `' . $table . '`';
        // Add where statement.
        $exec_parameters = [];
        if (null !== $where) {
            $query .= ' WHERE';
            foreach ($where as $i) {
                $query .= " {$i['logic_operator']} `{$i['column']}` " .
                    "{$i['operator']} :{$i['column']}";
                $exec_parameters[":{$i['column']}"] = $i['value'];
            }
        }
        // Ard ordering statement.
        if (null !== $order_by) {
            $query .= ' ORDER BY';
            foreach ($order_by as $k => $v) {
                $query .= " {$k} {$v}, ";
            }
            // Cut last ', '.
            $query = substr($query, 0, -2);
        }
        // Add limit value;
        if (null !== $limit) {
            $query .= ' LIMIT ' . $limit;
        }
        $query .= ';';
        $data = null;
        try {
            // Prepare query.
            $sth = $this->_connection
                ->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            // Execute query with passed parameters.
            $sth->execute($exec_parameters);
            // Get all rows.
            $data = $sth->fetchAll();
        } catch (PDOException $e) {
            echo $e->getCode() . ': ' . $e->getMessage();
        }
        return $data;
    }
    /**
     * Insert data into database;
     *
     * @param string $table Table name
     * @param array $data Data for inserting;
     *
     * @return bool Inserting status;
     */
    public function insert(string $table, array $data): bool
    {
        $token = str_repeat('?, ', count($data));
        $token = substr($token, 0, -2);
        $query = 'INSERT INTO `' . $table .
            '` (`' . implode(array_keys($data), '`, `') . '`)' .
            ' VALUES (' . $token . ');';
        $return = false;
        try {
            // Prepare query.
            $sth = $this->_connection
                ->prepare($query);
            // Execute query with passed parameters.
            $return = $sth->execute(array_values($data));
        } catch (PDOException $e) {
            echo $e->getCode() . ': ' . $e->getMessage();
        }
        return $return;
    }
    /**
     * Update data in database.
     *
     * @param string $table Table name.
     * @param array  $data  Data for updating.
     * @param array  $where Where statement.
     *
     * @return bool Updating status
     */
    public function update(string $table, array $data, array $where): bool
    {
        $query = 'UPDATE `' . $table .
            '` SET `' . implode(array_keys($data), '` = ?, `') . '` = ?';
        // Add where statement.
        $exec_parameters = array_values($data);
        if (null !== $where) {
            $query .= ' WHERE';
            foreach ($where as $i) {
                $query .= " {$i['logic_operator']} `{$i['column']}` " .
                    "{$i['operator']} ?";
                $exec_parameters[] = $i['value'];
            }
        }
        $return = false;
        try {
            // Prepare query.
            $sth = $this->_connection
                ->prepare($query);
            // Execute query with passed parameters.
            $return = $sth->execute($exec_parameters);
        } catch (PDOException $e) {
            echo $e->getCode() . ': ' . $e->getMessage();
        }
        return $return;
    }
    /**
     * Delete data from database;
     *
     * @param string $table Table name.
     * @param array  $where Where statement.
     *
     * @return bool
     */
    public function delete(string $table, array $where): bool
    {
        $query = 'DELETE FROM `' . $table . '` ';
        // Add where statement.
        $exec_parameters = [];
        if (null !== $where) {
            $query .= ' WHERE';
            foreach ($where as $i) {
                $query .= " {$i['logic_operator']} `{$i['column']}` " .
                    "{$i['operator']} :{$i['column']}";
                $exec_parameters[":{$i['column']}"] = $i['value'];
            }
        }
        $return = false;
        try {
            // Prepare query.
            $sth = $this->_connection
                ->prepare($query);
            // Execute query with passed parameters.
            $return = $sth->execute($exec_parameters);
        } catch (PDOException $e) {
            echo $e->getCode() . ': ' . $e->getMessage();
        }
        return $return;
    }
    /**
     * DataManager destructor for closing connection.
     */
    public function __destruct()
    {
        $this->_connection = null;
    }
}