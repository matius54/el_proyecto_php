<?php
require_once "./bytes.php";
/*
    DB es una clase wrapper para la clase PDO de php, su funcion principal es abtraer el proceso de conexion
    e interaccion con la base de datos, ademas de facilitar una forma para automatizar la creacion automatica
    de tablas e insercion automatica de registros tambien cambiando la configuracion "structure" y "finally"
    respectivamente en el archivo de configuracion db.json

    ademas de esto esta clase tiene opcionalmente proteccion contra inyecciones sql usando prepared statements
    al separar la consulta (query) de los argumentos (args) en el metodo execute

    y tiene proteccion contra XSS opcional (usando htmlspecialchars), en el metodo fetch y fetchAll,
    por defecto desactivado, pero se puede activar enviardole un true como argumento o un 
    (htmlspecialchars: true)
*/
class DB
{
    //esta variable guardara una unica instancia de esta clase,
    //ya que se trata de una clase singleton accesible a travez de DB::getInstance()
    private static ?DB $instance = null;

    //ubicacion del archivo de configuracion
    private String $configFile = "db.json"; /*----- Cambie la direccion antes era esta ./db.json -----*/

    //array del archivo de configuracion una vez decodificado del json
    private array $config;

    //variable de control de errores, solo sera true si ocurre un error
    private bool $error = false;

    //esta variable guardara una instancia de PDO
    private ?PDO $conn = null;

    //esta variable guardara una instancia de PDOStatement, basicamente el resultado
    private ?PDOStatement $result = null;

    /**
     * unica forma para obtener una instancia de DB, siguiendo el patron Singleton.
     * ni se te ocurra hacer un `new DB` que no funcionara, el constructor es privado intencionalmente.
     *
     * @return DB
     */
    static public function getInstance(): DB
    {
        if (self::$instance === null) {
            self::$instance = new DB;
        }
        return self::$instance;
    }

    private function __construct()
    {
        //se lee y decodifica el archivo de configuracion json en un array asociativo,
        //usando el parametro asociativo en `true` en la llamada de `json_decode()`
        /*
            TODO:
            - manejar errores por si falta el archivo de configuracion
            - añadir configuracion por defecto por si falta tambien
            - separar esta logica en otro metodo por ejemplo loadConfig()
        */
        $this->config = json_decode(json: file_get_contents(filename: $this->configFile), associative: true);
        try {
            //intenta conectar a la base de datos
            $this->connect();
            //si no hay errores
            $this->error = false;
        } catch (PDOException $e) {
            if ($e->getCode() !== 1049) {
                $this->showException($e, "No se ha podido establecer la conexion con la base de datos");
                die();
            }
            //si el error es 1049 significa que la base de datos no existe, entonces se crea usando el archivo json
            $this->initialize();
        }
    }

    public function __destruct()
    {
        //no hay que olvidarse de cerrar la conexion al final, por suerte php se puede encargar de eso con el destructor
        $this->close();
    }

    /**
     * Basicamente realiza acciones para inicializar la base de datos a partir del archivo json si esta no existe,
     * crea la base de datos `createDB()`, crea las tablas `createTables()` y ejecuta un sql extra en el `finally()`.
     *
     * @return void
     */
    private function initialize(): void
    {
        //todos estos metodos usan la configuracion del json
        //crea la base de datos 
        $this->createDB();
        //crea la estructura de tablas
        $this->createTables();
        //ejecuta algun sql adicional, en el finally
        $this->finally();
    }

    /**
     * Muestra directamente con echos, un mensaje en html indicando el error ocurrido, solo si esta habilitado en la configuracion.
     *
     * @param PDOException $e La excepción PDO que se produjo.
     * @param ?string $message Un mensaje personalizado para mostrar al usuario (opcional).
     * @param ?string $sql La consulta SQL que se estaba ejecutando cuando se produjo la excepción (opcional).
     * @param ?array $args Los argumentos que se estaban utilizando con la consulta SQL (opcional).
     *
     * @return void
     */
    protected function showException(
        PDOException $e,
        ?string $message = null,
        ?string $sql = null,
        ?array $args = null
    ): void {
        $this->error = true;
        if (!isset($this->config["showErrors"]) || $this->config["showErrors"] === false) throw $e;
        if ($message) echo "<h1>$message.</h1>";
        switch ($e->getCode()) {
            case 2002:
                echo "<h3>Revisa que el host este bien en en el archivo de configuracion: \"" . $this->configFile . "\".</h3>";
                break;
            case 1045:
                echo "<h3>Revisa que el usuario y contraseña esten bien en el archivo de configuracion: \"" . $this->configFile . "\".</h3>";
                break;
        }
        if ($sql) echo "<p>Solicitud SQL: $sql.</p>";
        if ($args) echo "<p>Argumentos SQL: [" . implode(", ", $args) . "].</p>";
        echo "<p>Codigo de error: " . $e->getCode() . ".</p>";
        echo "<p>Mensaje de error: " . $e->getMessage() . ".</p>";
    }

    /**
     * Este metodo se usa exclusivamente al ligar (bind) los parametros de entrada a los prepared statemets, se necesita obtener el valor del tipo a ligar
     * A partir de un parametro detecta su tipo y usando el enum dentro de la clase PDO se selecciona la opcion correspondiente.
     *
     * @param int|string|bool|Bytes|null $param El valor a analizar.
     *
     * @return int valor del `PDO::PARAM` correspondiente al `$param`.
     */
    private function getBindType(int|string|bool|Bytes|null $param): int
    {
        if (is_int($param)) {
            return PDO::PARAM_INT;
        } else if (is_string($param)) {
            return PDO::PARAM_STR;
        } else if (is_bool($param)) {
            return PDO::PARAM_BOOL;
        } else if ($param instanceof Bytes) {
            return PDO::PARAM_LOB;
        }
        return PDO::PARAM_NULL;
    }

    /**
     * Este metodo crea un objeto de conexion PDO y lo guarda en la propiedad conn del objeto,
     * el manejo de errores ocurre fuera de este, puede tirar un error de PDOException
     *
     * @return void
     */
    public function connect(): void
    {
        $this->conn = new PDO("mysql:host=" . $this->config["host"] . ";charset=utf8;dbname=" . $this->config["database"], $this->config["username"], $this->config["password"]);
    }

    /**
     * Este metodo crea un objeto de conexion `PDO` sin usar la base de datos como argumento
     * y lo usa para crear basado en la configuracion la base de datos ahora si
     * despues lo cierra e inicia una conexion con la base de datos, llamando al metodo `connect()`.
     * el manejo de errores ocurre fuera de este, puede tirar un error de PDOException
     *
     * @return void
     */
    private function createDB(): void
    {
        //nueva conexion sin usar el nombre de la base de datos `$dbname`
        $conn = new PDO("mysql:host=" . $this->config["host"], $this->config["username"], $this->config["password"]);
        //se toma el nombre desde el array de configuracion cargado
        $dbname = $this->config["database"];
        //usa el metodo `exec()` para crear la base de datos
        $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
        //cierra la conexion
        $conn = null;
        //y la vuelve a iniciar esta vez si usando la base de datos
        $this->connect();
    }

    /**
     * Este metodo usando la conexion a la base de datos existente crea todas las tablas especificadas en
     * el archivo de configuracion, `structure`, tambien hace manejo de errores simple
     *
     * @return void
     */
    private function createTables(): void
    {
        //por cada una de las tablas definidas en `structure`
        foreach ($this->config["structure"] as $table => $value) {
            try {
                //construye el sql correspondiente y lo ejecuta de 1
                //implode() hace que la lista quede en un solo string, en ese caso separado por comas ","
                $this->conn->exec("CREATE TABLE IF NOT EXISTS `$table` (" . implode(",", $value) . ")");
                //si no hay errores
                $this->error = false;
            } catch (PDOException $e) {
                $this->showException($e, "Ha ocurrido un error creando la tabla \"$table\" automaticamente");
            }
        }
    }

    /**
     * Este metodo usando la conexion a la base de datos existente ejecuta todos los strings
     * el archivo de configuracion, `finally` como sql, tambien hace un manejo de errores simple
     *
     * @return void
     */
    private function finally(): void
    {
        foreach ($this->config["finally"] as $value) {
            try {
                $this->conn->query($value);
                //si no hay errores
                $this->error = false;
            } catch (PDOException $e) {
                $this->showException($e, "Ha ocurrido un error ejecutando el bloque finally", $value);
            }
        }
    }

    /**
     * Cierra la conexion del objeto PDO
     *
     * @return void
     */
    public function close(): void
    {
        $this->conn = null;
    }

    /**
     * Ejecuta una consulta SQL preparada.
     *
     * - Este método prepara la consulta SQL proporcionada, liga cualquier argumento 
     * proporcionado a los marcadores de parámetros y luego la ejecuta.
     * - El resultado de la consulta se almacena internamente para su uso posterior con métodos 
     * como `fetch` y `fetchAll`.
     *
     * - En caso de ocurrir un error durante la ejecución de la consulta, se lanza una 
     * excepción `PDOException` y se llama al método `showException` para decidir si mostrarlo o no en el html.
     *
     * @param string $sql El string que contiene la consulta SQL.
     * @param ?array $arg La lista de argumentos, o valores para ser ligados a la consulta. (opcional)
     *
     * @return void
     */
    public function execute(string $sql, string|int|float|bool|array|null|Bytes ...$arg): void
    {
        //esto es un arreglo para hacer que se
        //puedan insertar float en la base de datos.
        $index = 0;
        if ($arg and is_array($arg[0])) $arg = $arg[0];
        foreach ($arg as $key => $value) {
            $index = strpos($sql, "?", $index + 1);
            if (is_float($value) or is_double($value)) {
                $a = substr($sql, 0, $index);
                $b = substr($sql, $index + 1);
                $sql = $a . $value . $b;
                unset($arg[$key]);
            }
        }
        $arg = array_values($arg);
        try {
            $prepared = $this->conn->prepare($sql);
            foreach ($arg as $index => $value) {
                $prepared->bindValue($index + 1, $value, $this->getBindType($value));
            }
            $prepared->execute();
            $this->result = $prepared;
            $this->error = false;
        } catch (PDOException $e) {
            $this->showException($e, "Ha ocurrido un error ejecutando SQL", $sql, $arg);
        }
    }

    /**
     * Este metodo devuelve una sola fila del resultado de la consulta.
     * - Es útil para recorrer el resultado de la consulta fila por fila.
     * - Se puede usar en un bucle while para procesar cada fila del resultado.
     * - Obtiene una sola fila del resultado, en forma de array asociativo.
     *
     * @param bool $htmlspecialchars si es `true` se le aplica la funcion `htmlspecialchars()` a todos los valores del resultado para proteger contra XSS.
     *
     * @return array una fila del resultado, o un array vacio.
     */
    public function fetch(bool $htmlspecialchars = false): array
    {
        if ($this->result === null) return [];
        $result = $this->result->fetch(PDO::FETCH_ASSOC);
        if (!is_array($result)) $result = [];
        if ($htmlspecialchars) {
            foreach ($result as $key => $value) {
                $result[$key] = is_string($value) ? htmlspecialchars($value) : $value;
            }
        }
        return $result;
    }

    /**
     * Este metodo devuelve todas las filas del resultado de la consulta.
     * - Es útil para obtener todos los datos del resultado de la consulta de una sola vez.
     * - Se puede usar para almacenar el resultado en una variable o para procesarlo de otra manera.
     * - Obtiene una lista de todas las filas del resultado, en forma de lista de arrays asociativos.
     *
     * @param bool $htmlspecialchars si es `true` se le aplica la funcion `htmlspecialchars()` a todos los valores del resultado para proteger contra XSS.
     *
     * @return array un array de todas las filas del resultado, o un array vacio.
     */
    public function fetchAll(bool $htmlspecialchars = false): array
    {
        if ($this->result === null) return [];
        $data = $this->result->fetchAll(PDO::FETCH_ASSOC);
        if ($htmlspecialchars && is_array($data)) {
            foreach ($data as $index => $element) {
                if (!is_array($data)) continue;
                foreach ($element as $key => $value) {
                    $element[$key] = is_string($value) ? htmlspecialchars($value) : $value;
                }
                $data[$index] = $element;
            }
        }
        return $data;
    }

    /**
     * Obtiene el número de filas afectadas por la última consulta ejecutada.
     *
     * - Este método solo es válido para operaciones de inserción, borrado o actualización (DML: DELETE, INSERT, UPDATE).
     * - Si la consulta anterior fue una selección (SELECT) o no se ha ejecutado ninguna consulta, el método devolverá `null`. 
     *
     * @return int|null El número de filas afectadas o `null` si no aplica.
     */
    public function rowCount(): int | null
    {
        if ($this->result === null) return null;
        return $this->result->rowCount();
    }

    /**
     * Inicia una transacción en la base de datos.
     *
     * - Una transacción permite agrupar varias operaciones como una unidad atómica. 
     * - Si alguna operación falla, se pueden deshacer todos los cambios realizados 
     * dentro de la transacción mediante el método `rollback()`.
     *
     * - Este método devuelve `true` si la transacción se inicia con éxito, `false` en caso contrario.
     *
     * @return bool `true` si la transacción se inicia correctamente, `false` en caso de error.
     */
    public function beginTransaction(): bool
    {
        return $this->conn->beginTransaction();
    }

    /**
     * Deshace los cambios realizados dentro de una transacción abierta.
     *
     * - Este método solo tiene efecto si previamente se ha iniciado una transacción 
     * con `beginTransaction()`. Si no hay transacción activa, no tiene ningún efecto.
     *
     * - El método devuelve `true` si la transacción se deshace con éxito, `false` en caso contrario.
     *
     * @return bool `true` si la transacción se deshace correctamente, `false` en caso de error.
     */
    public function rollback(): bool
    {   
        try {
            return $this->conn->rollback();
        } catch (PDOException $e){
            return false;
        }
    }

    /**
     * Confirma los cambios realizados dentro de una transacción abierta.
     *
     * - Este método solo tiene efecto si previamente se ha iniciado una transacción 
     * con `beginTransaction()`. Si no hay transacción activa, no tiene ningún efecto.
     *
     * - El método devuelve `true` si la transacción se confirma con éxito, `false` en caso contrario.
     *
     * @return bool `true` si la transacción se confirma correctamente, `false` en caso de error.
     */
    public function commit(): bool
    {
        return $this->conn->commit();
    }

    /**
     * Indica si ha ocurrido un error en la última operación realizada.
     *
     * - Este método devuelve `true` si se ha registrado un error interno 
     * durante la ejecución de la última consulta o transacción.
     * - De lo contrario, devuelve `false`.
     *
     * @return bool `true` si hay un error registrado, `false` en caso contrario.
     */
    public function error(): bool
    {
        return $this->error;
    }
    //obtiene el id del ultimo registro añadido a la tabla que indiques
    public function getLastId(string $tableName, string $idName = "id") : int|null {
        $this->execute("SELECT `$idName` FROM `$tableName` ORDER BY `$idName` DESC LIMIT 1 OFFSET 0",[]);
        return $this->fetch()[$idName] ?? null;
    }
    // 1 => (?), 4 => (?,?,?,?), 6 => (?,?,?,?,?,?)
    private static function getPlaceholders(int $amount) : string {
        //return  "(".implode(", ", array_fill(0, $amount, "?")).")";
        //this is more efficient
        $placeholders = "(";
        for($i = 1; $i < $amount * 2; $i++)
            $placeholders[$i] = $i % 2 ? "?" : ",";
        return $placeholders . ")";
    }
    private static function quoteBT(array $arr) : array {
        return array_map(function ($col) {return "`$col`";}, $arr);
    }
    //no soporta joins (aun)
    public function select(
        string $table,
        array $columns = [],
        string $condition = "",
        array $args = [],
        int $limit = 1024,
        int $offset = 0,
        bool $all = false,
        bool $htmlspecialchars = false
    ): array {
        $sql = "SELECT ";
        $sql .= $columns ? implode(", ", self::quoteBT($columns)) : "*";
        $sql .= " FROM `$table`";
        if ($condition) $sql .= " WHERE $condition";
        if (!$all) $limit = 1;
        $sql .= " LIMIT $limit OFFSET $offset";
        $this->execute($sql, $args);
        if ($all) {
            return $this->fetchAll($htmlspecialchars);
        } else {
            return $this->fetch($htmlspecialchars);
        }
    }
    public function insert(
        string $table,
        array $data
    ): int|null {
        $sql = "INSERT INTO `$table` (";
        $sql .= implode(
            ", ",
            self::quoteBT(array_keys($data))
        );
        $sql .= ") VALUES " . self::getPlaceholders(sizeof($data));
        $this->execute($sql, $data);
        return $this->rowCount();
    }
    public function update(
        string $table,
        array $data,
        string $condition,
        array $args = []
    ): int|null {
        $sql = "UPDATE `$table` SET ";
        $sql .= implode(
            ", ",
            array_map(
                function ($col) {
                    return "`$col` = ?";
                },
                array_keys($data)
            )
        );
        if ($condition) $sql .= " WHERE $condition";
        $this->execute($sql, array_merge($data, $args));
        return $this->rowCount();
    }
    public function delete(
        string $table,
        string $condition,
        array $args = []
    ): int|null {
        $sql = "DELETE FROM `$table` WHERE $condition";
        $this->execute($sql, $args);
        return $this->rowCount();
    }
}