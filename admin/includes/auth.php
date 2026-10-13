<?php
/**
 * Global Connect - Admin Authentication Enforcer
 */
require_once __DIR__ . '/../../includes/functions.php';

require_auth();
$currentUser = current_user();
