<?php
include_once(dirname(__FILE__) . "/SimpleOrm.class.php");

$conn = new mysqli('localhost', 'grease_rat', 'grease_rat');

if ($conn->connect_error)
{
    die(sprintf('Unable to connect to the database. %s', $conn->connect_error));
}

if (!$conn->set_charset("utf8"))
{
    die( printf("Ошибка при загрузке набора символов utf8: %s\n", $conn->error));
}

SimpleOrm::useConnection($conn, 'grease_rat');