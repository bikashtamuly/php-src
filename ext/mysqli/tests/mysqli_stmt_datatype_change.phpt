--TEST--
mysqli_stmt_bind_param() - playing with references
--SKIPIF--
<?php
require_once('skipif.inc');
require_once('skipifemb.inc');
require_once('skipifconnectfailure.inc');
?>
--FILE--
<?php
	include "connect.inc";
	require('table.inc');

	if (!$c1 = mysqli_connect($host, $user, $passwd, $db, $port, $socket)) {
		printf("Cannot connect to the server using host=%s, user=%s, passwd=***, dbname=%s, port=%s, socket=%s\n",
			$host, $user, $db, $port, $socket);
		exit(1);
	}
	if (!$c2 = mysqli_connect($host, $user, $passwd, $db, $port, $socket)) {
		printf("Cannot connect to the server using host=%s, user=%s, passwd=***, dbname=%s, port=%s, socket=%s\n",
			$host, $user, $db, $port, $socket);
		exit(1);
	}

	$c1->query("use $db");
	$c2->query("use $db");
	$c1->query("drop table if exists type_change");
	$c1->query("create table type_change(a int, b char(10))");
	$c1->query("insert into type_change values (1, 'one'), (2, 'two')"); 
	$s1 = $c1->prepare("select a from type_change order by a");
	var_dump($s1);
	var_dump($s1->execute(), $s1->bind_result($col1));
	echo "---- Row 1\n";
	var_dump($s1->fetch());
	var_dump($col1);
	echo "---- Row 2\n";
	var_dump($s1->fetch());
	var_dump($col1);
	echo "---- Row 3\n";
	var_dump($s1->fetch());
	echo "----\n";

	echo "ALTER\n";
	var_dump($c2->query("alter table type_change drop a"));
	var_dump($s1->execute());
	var_dump($c1->error);

	echo "---- Row 1\n";
	var_dump($s1->fetch());
	var_dump($col1);
	echo "---- Row 2\n";
	var_dump($s1->fetch());
	var_dump($col1);
	echo "---- Row 3\n";
	var_dump($s1->fetch());
	echo "----\n";

	echo "done!";
?>
--EXPECTF--
object(mysqli_stmt)#%d (%d) {
  ["affected_rows"]=>
  int(0)
  ["insert_id"]=>
  int(0)
  ["num_rows"]=>
  int(0)
  ["param_count"]=>
  int(0)
  ["field_count"]=>
  int(1)
  ["errno"]=>
  int(0)
  ["error"]=>
  string(0) ""
  ["sqlstate"]=>
  string(5) "00000"
  ["id"]=>
  int(1)
}
bool(true)
bool(true)
---- Row 1
bool(true)
int(1)
---- Row 2
bool(true)
int(2)
---- Row 3
NULL
----
ALTER
bool(true)
bool(false)
string(34) "Unknown column 'a' in 'field list'"
---- Row 1
bool(false)
int(2)
---- Row 2
bool(false)
int(2)
---- Row 3
bool(false)
----
done!
--UEXPECTF--
object(mysqli_stmt)#%d (%d) {
  [u"affected_rows"]=>
  int(0)
  [u"insert_id"]=>
  int(0)
  [u"num_rows"]=>
  int(0)
  [u"param_count"]=>
  int(0)
  [u"field_count"]=>
  int(1)
  [u"errno"]=>
  int(0)
  [u"error"]=>
  unicode(0) ""
  [u"sqlstate"]=>
  unicode(5) "00000"
  [u"id"]=>
  int(1)
}
bool(true)
bool(true)
---- Row 1
bool(true)
int(1)
---- Row 2
bool(true)
int(2)
---- Row 3
NULL
----
ALTER
bool(true)
bool(false)
unicode(34) "Unknown column 'a' in 'field list'"
---- Row 1
bool(false)
int(2)
---- Row 2
bool(false)
int(2)
---- Row 3
bool(false)
----
done!
