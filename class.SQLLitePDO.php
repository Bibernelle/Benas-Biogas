<?php
class SQLitePDO extends PDO {
    function __construct($filename) {
        $filename = realpath($filename);
        parent::__construct('sqlite:' . $filename);

        $key = ftok($filename, 'a');
        $this->sem = sem_get($key);
    }

    function beginTransaction() {
        sem_acquire($this->sem);
        return parent::beginTransaction();
    }

    function commit() {
        $success = parent::commit();
        sem_release($this->sem);
        return $success;
    }

    function rollBack() {
        $success = parent::rollBack();
        sem_release($this->sem);
        return $success;
    }
}
?>