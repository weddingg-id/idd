<?php
session_start();
if (isset($_SESSION['scrolled']) && $_SESSION['scrolled']) {
  echo "1";
  $_SESSION['scrolled'] = false;
} else {
  echo "0";
}
?>
