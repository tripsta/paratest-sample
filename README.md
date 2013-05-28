# Intro
This examples demonstrates the functional mode and the usage of TEST_TOKEN environment variable of paratest.

[Paratest][1] is a PHP Package that enables PHPUnit tests to run in parallel thus reducing the overall execution time of the test suite.

# Paratest Functional mode
By default paratest opens up 5 processes, each of them running the tests of a single file (test class) of the Test Suite.

Once a process is complete, it closes and another process can open to process another file.
This goes on until all test files are processed.

Some times (usually in Functional Tests) there are many slow tests gathered in one test class.
In this case one process will run all the tests of this class sequentially and will probably delay the total execution time when all other tests have been completed.

That's where functional mode comes in.
In functional mode, the test suite breaks down in test methods instead of test classes. So when a test class contains many slow tests they will be distributed among all processes.

> To run paratest in functional mode add the `-f` option.
ex: `vendor/bin/paratest -f <test-dir>`

# Sharing common resources
One issue that often comes up is parallel processing sharing the same resources.
Some examples are:
* Database  (on a mysql database, Deadlocks can happen when two concurrent processes, try to lock the same tables)
* Filesystem (one process modifies the contents of a file, that another process expects to find unchanged)
* Caching (one process modifies the contents of a cache key, that another process expects to find unchanged )

The above scenarios should not happen on unit tests where, ideally, no external resources are used.
They might happen on integration tests and are very likely to happen on functional tests.


# TEST_TOKEN environment variable
TEST_TOKEN tries to deal with the common resources issue in a very simple way:
> Clone the resources to ensure that no concurrent processes will access the same resource

It is accesible from the test suite itself `getenv('TEST_TOKEN')`. It uniquely identifies each running process opened by paratest in a *zero-based* index.
Since TEST_TOKEN is unique per running process, it can aid in the resource creation to avoid parallel processing accessing the same resource.
When a paratest process stops, the TEST_TOKEN is released and a new process will get it.

# FileTest example
The example test class in `Tests\FileTest.php` consists of 20 slow tests that all write to a file, wait 200 milliseconds and then read from this file expecting to find what they wrote. What they write contain a random number `rand(1,1000)`

The TEST_TOKEN environment variable is used as a suffix of the filename that each test will use

    $this->_filename = sprintf('out%s.txt', getenv('TEST_TOKEN'));

## setup the example
Clone this repo and run `composer update` 
This will install both PHPUnit and paratest in the `vendor` folder and the binaries for both will be in `vendor/bin` folder

## Run the example
### PHPUnit
`vendor/bin/phpunit test/FileTest.php`

### Paratest with functional mode on
`vendor/bin/paratest -f test/FileTest.php`

### Paratest processes accessing the same resource
Modify the filename to be independent of the TEST_TOKEN variable
Change this line in `File_Test::setUp`:

    $this->_filename = sprintf('out%s.txt', getenv('TEST_TOKEN'));

to this one:

    $this->_filename = sprintf('out.txt');

Then run:

    vendor/bin/paratest -f test/FileTest.php

Many errors like this one shall occur:
```shell
Failed asserting that two strings are equal.
--- Expected
+++ Actual
@@ @@
-'here is a random number 580'
+'here is a random number 755'
```

# TEST_TOKEN usage in databases
TEST_TOKEN can be used when having issues with tests running in parallel on a single database.
The following steps are required:

1. Create as many test databases as paratest processes (clones of the main test database) to ensure that each process will run against its own database
A script demonstrating how to do that can be found at [tripsta/wink-clone-databases][1], tailored for Zend Framework 1.x applications
2. In your application use `getenv('TEST_TOKEN')` to construct the database name that the process will connect to.

An example for Zend Framework 1.x
```php
	$testToken = getenv('TEST_TOKEN');
	if (is_numeric($testToken)) {
		$config->params->dbname .= sprintf("%s", (int)$testToken+1);
	}
	return $config;
```
Which will convert a database name "testdb" to testdb1, testdb2 etc.
> Note that the code above increments the TEST_TOKEN by one to convert the zero-based index to one-based index.


[1]: https://github.com/brianium/paratest "Paratest"
[2]: https://github.com/tripsta/wink-clone-databases "Clone databases"