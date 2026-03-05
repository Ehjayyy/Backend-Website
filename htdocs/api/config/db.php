<?php
$DB_HOST = "sql207.infinityfree.com";
$DB_USER = "if0_41302424";
$DB_PASS = "Obanana0917203";
$DB_NAME = "if0_41302424_marketplace";

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
  die("DB connection failed: " . $conn->connect_error);
}