# Symphony Tests

The Symphony Tests extension is a Symphony frontend for the [SimpleTest][http://simpletest.org/en/start-testing.html] unit testing framework.

## Installation

1.	Upload the `symphony_tests` folder in this archive to your Symphony `/extensions` folder.
2.	Enable it by selecting the "Symphony Tests" extension, choose Enable from the with-selected menu, then click Apply.
3.	Browse to System > Tests to view any unit tests currently installed.


## Adding unit tests

You can write your own unit tests, just put your standard unit test class in:

1.	`workspace/tests/test.classname.php`
2.	`symphony/tests/test.classname.php`
3.	`extensions/yourextension/tests/test.classname.php`

You also need to prefix your class name with `SymphonyTest`. Take a look in the `tests/test.google-symphony.php` file for a basic test example.
