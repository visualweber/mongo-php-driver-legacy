--TEST--
Test for PHP-1149: Database name validation
--SKIPIF--
<?php require_once "tests/utils/standalone.inc" ?>
--FILE--
<?php
require_once "tests/utils/server.inc";

$host = MongoShellServer::getStandaloneInfo();

$m = new MongoClient( $host );

$dbNamesToTry = array(
	'', '"', '/', '\\', ' ', "\0",
	'foo', 'bar',
	'this is a really long database name which is also not allowed, because, well, it is way too long',
	"name\0with\0",
);

foreach ( $dbNamesToTry as $name )
{
	echo $name, ': ';
	try {
		$d = $m->selectDb( $name );
		echo "OKAY\n";
	} catch ( MongoException $e ) {
		echo 'EXCEPTION: ', $e->getMessage(), "\n";
	}
}
?>
--EXPECT--
: EXCEPTION: Database name cannot be empty
": OKAY
/: EXCEPTION: Database name contains invalid characters: /
\: EXCEPTION: Database name contains invalid characters: \
 : EXCEPTION: Database name contains invalid characters:  
 : EXCEPTION: Database name cannot contain null bytes: \0...
foo: OKAY
bar: OKAY
this is a really long database name which is also not allowed, because, well, it is way too long: EXCEPTION: Database name cannot exceed 63 characters: this is a really long database name which is also not allowed, because, well, it is way too long
name with : EXCEPTION: Database name cannot contain null bytes: name\0...
