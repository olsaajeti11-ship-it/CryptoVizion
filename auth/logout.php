<?php
require_once __DIR__ . '/../db.php';

session_unset();
session_destroy();

redirect('/index.php');
