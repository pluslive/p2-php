<?php

namespace ImageCache2\Console\Command;

use Symfony\Component\Console\Command\Command as sfConsoleCommand;
use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    Symfony\Component\Console\Formatter\OutputFormatterStyle;
use PEAR;

require_once P2EX_LIB_DIR . '/ImageCache2/bootstrap.php';

class Setup extends sfConsoleCommand
{
    const PG_TRGM_GIST  = 'gist';
    const PG_TRGM_GIN   = 'gin';

    // {{{ properties

    /**
     * @var array
     */
    private $config;

    /**
     * @var bool
     */
    private $dryRun;

    /**
     * @var string
     */
    private $pgTrgm;

    /**
     * @var DB_common
     */
    private $db;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $serialPriamryKey;

    /**
     * @var string
     */
    private $tableExtraDefs;

    /**
     * @var int
     */
    private $findTableStatement;

    /**
     * @var string
     */
    private $findIndexFormat;

    // }}}

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command\Command::configure()
     */
    protected function configure()
    {
        $this
        ->setName('setup')
        ->setDescription('Setups ImageCache2 environment')
        ->setDefinition(array(
            new InputOption('check-only', null, InputOption::VALUE_NONE, 'Don\'t execute anything'),
            new InputOption('pg-trgm', null, InputOption::VALUE_REQUIRED, 'Enable gist or gin 3-gram index'),
        ));
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Console\Command\Command::execute()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->config = ic2_loadconfig();
        $this->dryRun = (bool)$input->getOption('check-only');
        $this->pgTrgm = $input->getOption('pg-trgm');
        $this->output = $output;

        if ($this->checkConfiguration()) {
            $result = $this->connect();
            if ($result) {
                $this->info('Database: OK');
                $this->serialPriamryKey = $result[0];
                $this->tableExtraDefs = $result[1];
                $this->createTables();
                $this->createIndexes();
            }
        }
    }

    /**
     * @return bool
     */
    private function checkConfiguration()
    {
        $result = true;

        $enabled = $GLOBALS['_conf']['expack.ic2.enabled'];
        $dsn = $this->config['General']['dsn'];
        $driver = $this->config['General']['driver'];

        $this->comment('enabled=' . var_export($enabled, true));
        $this->comment('dsn=' . var_export($dsn, true));
        $this->comment('driver=' . var_export($driver, true));

        if (!$enabled) {
            $this->error("\$_conf['expack.ic2.enabled'] is not enabled in conf/conf_admin_ex.inc.php.");
            $result = false;
        }

        if (!$dsn) {
            $this->error("\$_conf['expack.ic2.general.dsn'] is not set in conf/conf_ic2.inc.php.");
            $result = false;
        }

        $driver = strtolower($driver);
        switch ($driver) {
            case 'imagemagick6':
            case 'imagemagick':
                if (!ic2_findexec('convert', $this->config['General']['magick'])) {
                    $this->error("Command 'convert' is not found");
                    $result = false;
                } else {
                    $this->info('Image Driver: OK');
                }
                break;
            case 'gd':
            case 'imagick':
            case 'imlib2':
                if (!extension_loaded($driver)) {
                    $this->error("Extension {$driver} is not loaded");
                    $result = false;
                } else {
                    $this->info('Image Driver: OK');
                }
                break;
            default:
                $this->error('Unknow image driver.');
                $result = false;
        }

        return $result;
    }

    /**
     * @return array
     */
    private function connect()
    {
        $phptype = null;

        $dsn = $this->config['General']['dsn'];

        if (preg_match('/^(\w+)(?:\((\w+)\))?:/', $dsn, $matches)) {
            $phptype = strtolower($matches[1]);
        }

        if (!in_array($phptype, array('mysql', 'mysqli', 'pgsql', 'sqlite'))) {
            $this->error('Supports only MySQL, PostgreSQL and SQLite2.');
            return null;
        }

        if (!extension_loaded($phptype)) {
            $this->error("Extension '{$phptype}' is not loaded.");
            return null;
        }

        $db = \DB::connect($dsn);
        if (PEAR::isError($db)) {
            $this->error($db->getMessage());
            return null;
        }

        $this->db = $db;

        return $this->postConnect($phptype);
    }

    // {{{ post connect methods

    private function postConnect($phptype)
    {
        $result = null;

        switch ($phptype) {
            case 'mysql':
                $result = $this->postConnectMysql(false);
                break;
            case 'mysqli':
                $result = $this->postConnectMysql(true);
                break;
            case 'pgsql':
                $result = $this->postConnectPgsql();
                break;
            case 'sqlite':
                $result = $this->postConnectSqlite();
                break;
        }

        return $result;
    }

    private function postConnectMysql($mysqli)
    {
        $serialPriamryKey = 'INTEGER PRIMARY KEY AUTO_INCREMENT';
        $tableExtraDefs = ' TYPE=MyISAM';

        $db = $this->db;
        $result = $db->getRow("SHOW VARIABLES LIKE 'version'",
                              array(), DB_FETCHMODE_ORDERED);
        if (is_array($result)) {
            $version = $result[1];
            if (version_compare($version, '4.1.2', 'ge')) {
                $tableExtraDefs = ' ENGINE=MyISAM DEFAULT CHARACTER SET utf8';
            }
        }

        if (!$this->dryRun) {
            if ($mysqli && function_exists('mysqli_set_charset')) {
                mysqli_set_charset($db->connection, 'utf8');
            } elseif (!$mysqli && function_exists('mysql_set_charset')) {
                mysql_set_charset('utf8', $db->connection);
            } else {
                $db->query('SET NAMES utf8');
            }
        }

        $stmt = $db->prepare('SHOW TABLES LIKE ?');
        if (PEAR::isError($stmt)) {
            $this->error($stmt->getMessage());
            return null;
        }
        $this->findTableStatement = $stmt;
        $this->findIndexFormat = 'SHOW INDEX FROM %s WHERE Key_name LIKE ?';

        return array($serialPriamryKey, $tableExtraDefs);
    }

    private function postConnectPgsql()
    {
        $serialPriamryKey = 'SERIAL PRIMARY KEY';
        $tableExtraDefs = '';

        $db = $this->db;

        if (!$this->dryRun) {
            if (function_exists('pg_set_client_encoding')) {
                pg_set_client_encoding($db->connection, 'UNICODE');
            } else {
                $db->query("SET CLIENT_ENCODING TO 'UNICODE'");
            }
        }

        $stmt = $db->prepare("SELECT relname FROM pg_class WHERE relkind = 'r' AND relname = ?");
        if (PEAR::isError($stmt)) {
            $this->error($stmt->getMessage());
            return null;
        }
        $this->findTableStatement = $stmt;
        $this->findIndexFormat = "SELECT relname FROM pg_class WHERE relkind = 'i' AND relname = ?";

        return array($serialPriamryKey, $tableExtraDefs);
    }

    private function postConnectSqlite()
    {
        $serialPriamryKey = 'INTEGER PRIMARY KEY';
        $tableExtraDefs = '';

        $db = $this->db;

        $stmt = $db->prepare("SELECT name FROM sqlite_master WHERE type = 'table' AND name= ?");
        if (PEAR::isError($stmt)) {
            $this->error($stmt->getMessage());
            return null;
        }
        $this->findIndexFormat = "SELECT name FROM sqlite_master WHERE type = 'index' AND name= ?";

        return array($serialPriamryKey, $tableExtraDefs);
    }

    // }}}
    // {{{ methods to create table

    private function createTables()
    {
        $imagesTable = $this->config['General']['table'];
        $errorLogTable = $this->config['General']['error_table'];
        $blackListTable = $this->config['General']['blacklist_table'];

        if ($this->findTable($imagesTable)) {
            $this->info("Table '{$imagesTable}' already exists");
        } else {
            $this->createImagesTable($imagesTable);
        }

        if ($this->findTable($errorLogTable)) {
            $this->info("Table '{$errorLogTable}' already exists");
        } else {
            $this->createErrorLogTable($errorLogTable);
        }

        if ($this->findTable($blackListTable)) {
            $this->info("Table '{$blackListTable}' already exists");
        } else {
            $this->createBlackListTable($blackListTable);
        }
    }

    private function findTable($tableName)
    {
        $result = $this->db->execute($this->findTableStatement, array($tableName));
        if (PEAR::isError($result)) {
            $this->error($result->getMessage());
            return false;
        }
        return $result->numRows() > 0;
    }

    private function doCreateTable($tableName, $sql)
    {
        if ($this->dryRun) {
            $this->comment($sql);
            return true;
        }

        $result = $this->db->query($sql);
        if (PEAR::isError($result)) {
            $this->error($result->getMessage());
            return false;
        }

        $this->info("Table '{$tableName}' created");
        return true;
    }

    private function createImagesTable($tableName)
    {
        $quotedTableName = $this->db->quoteIdentifier($tableName);
        $sql = <<<SQL
CREATE TABLE {$quotedTableName} (
    id     {$this->serialPriamryKey},
    uri    VARCHAR (255),
    host   VARCHAR (255),
    name   VARCHAR (255),
    size   INTEGER NOT NULL,
    md5    CHAR (32) NOT NULL,
    width  SMALLINT NOT NULL,
    height SMALLINT NOT NULL,
    mime   VARCHAR (50) NOT NULL,
    time   INTEGER NOT NULL,
    rank   SMALLINT NOT NULL DEFAULT 0,
    memo   TEXT
){$this->tableExtraDefs};
SQL;
        return $this->doCreateTable($tableName, $sql);
    }

    private function createErrorLogTable($tableName)
    {
        $quotedTableName = $this->db->quoteIdentifier($tableName);
        $sql = <<<SQL
CREATE TABLE {$quotedTableName} (
    uri     VARCHAR (255),
    errcode VARCHAR(64) NOT NULL,
    errmsg  TEXT,
    occured INTEGER NOT NULL
){$this->tableExtraDefs};
SQL;
        return $this->doCreateTable($tableName, $sql);
    }

    private function createBlackListTable($tableName)
    {
        $quotedTableName = $this->db->quoteIdentifier($tableName);
        $sql = <<<SQL
CREATE TABLE {$quotedTableName} (
    id     {$this->serialPriamryKey},
    uri    VARCHAR (255),
    size   INTEGER NOT NULL,
    md5    CHAR (32) NOT NULL,
    type   SMALLINT NOT NULL DEFAULT 0
){$this->tableExtraDefs};
SQL;
        return $this->doCreateTable($tableName, $sql);
    }

    // }}}
    // {{{ methods to create index

    private function createIndexes()
    {
        $imagesTable = $this->config['General']['table'];
        $errorLogTable = $this->config['General']['error_table'];
        $blackListTable = $this->config['General']['blacklist_table'];

        $indexes = array(
            $imagesTable => array(
                '_uri' => array('uri'),
                '_time' => array('time'),
                '_unique' => array('size', 'md5', 'mime'),
            ),
            $errorLogTable => array(
                '_uri' => array('uri'),
            ),
            $blackListTable => array(
                '_uri' => array('uri'),
                '_unique' => array('size', 'md5'),
            ),
        );

        foreach ($indexes as $tableName => $indexList) {
            foreach ($indexList as $indexNameSuffix => $fieldNames) {
                $indexName = 'idx_' . $tableName . $indexNameSuffix;
                if ($this->findIndex($indexName, $tableName)) {
                    $this->info("Index '{$indexName}' already exists");
                } else {
                    $this->doCreateIndex($indexName, $tableName, $fieldNames);
                }
            }
        }

        if (strcasecmp(get_class($this->db), 'db_pgsql') === 0) {
            $pgTrgm = $this->pgTrgm;
            if ($pgTrgm === self::PG_TRGM_GIST ||
                $pgTrgm === self::PG_TRGM_GIN) {
                $indexName = 'idx_memo_tgrm';
                if ($this->findIndex($indexName, $imagesTable)) {
                    $this->info("Index '{$indexName}' already exists");
                } else {
                    $this->doCreatePgTrgmIndex($pgTrgm, $indexName,
                                               $imagesTable, 'memo');
                }
            }
        }
    }

    private function doCreateIndex($indexName, $tableName, array $fieldNames)
    {
        $db = $this->db;
        $callback = array($db, 'quoteIdentifier');
        $sql = sprintf('CREATE INDEX %s ON %s (%s);',
                       $db->quoteIdentifier($indexName),
                       $db->quoteIdentifier($tableName),
                       implode(', ', array_map($callback, $fieldNames)));

        if ($this->dryRun) {
            $this->comment($sql);
            return true;
        }

        $result = $db->query($sql);
        if (PEAR::isError($result)) {
            $this->error($result->getMessage());
            return false;
        }

        $this->info("Index '{$indexName}' created");

        return true;
    }

    private function doCreatePgTrgmIndex($indexType, $indexName, $tableName, $fieldName)
    {
        $db = $this->db;
        $sql = sprintf('CREATE INDEX %2$s ON %3$s USING %1$s (%4$s %1$s_trgm_ops);',
                       $indexType,
                       $db->quoteIdentifier($indexName),
                       $db->quoteIdentifier($tableName),
                       $db->quoteIdentifier($fieldName));

        if ($this->dryRun) {
            $this->comment($sql);
            return true;
        }

        $result = $db->query($sql);
        if (PEAR::isError($result)) {
            $this->error($result->getMessage());
            return false;
        }

        $this->info("{$indexType} Index '{$indexName}' created");

        return true;
    }

    private function findIndex($indexName, $tableName)
    {
        $db = $this->db;
        $sql = sprintf($this->findIndexFormat,
                       $db->quoteIdentifier($tableName));
        $result = $db->query($sql, array($indexName));
        if (PEAR::isError($result)) {
            $this->error($result->getMessage());
            return false;
        }
        return $result->numRows() > 0;
    }

    // }}}
    // {{{ console output methods

    private function info($message)
    {
        $this->output->writeln("<info>{$message}</info>");
    }

    private function comment($message)
    {
        $this->output->writeln("<comment>{$message}</comment>");
    }

    private function error($message)
    {
        if ($this->dryRun) {
            $this->output->writeln("<error>{$message}</error>");
        } else {
            throw new \Exception($message);
        }
    }

    // }}}
}
