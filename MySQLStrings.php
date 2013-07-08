<?php
// SQL Strings
$sCreateDB = 'CREATE DATABASE IF NOT EXISTS twitterFeed';
$sCreateTable = 'CREATE TABLE IF NOT EXISTS tweetData' .
				'(user VARCHAR(20), text VARCHAR(140), ' .
					'location VARCHAR(40), datetime VARCHAR(40), id_str VARCHAR(20))';
//$sInsertNextIDs = 
					