<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/categories.php';

session_start_safe();
$categories = categories_all();
// ... HTML с <?= include header.php
// 
// ... foreach $categories -> карточка категории (ссылка на /catalog.php?slug=...) ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Main</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Golos+Text&display=swap" rel="stylesheet">
</head>
<body>
<div class="all">
    <div class="header">
        <div class="logo">
           <?php include __DIR__ . '/includes/header.php'; ?>
        </div>

        <div class="authorization">
            <div class="menu" id="menu">
            </div>
        </div>
    </div>
</div>