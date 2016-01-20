# base-theme
WordPress theme for SDES Template Rev. 2015 layout.


# Development Toolset
Overview of recommended development tools.

## Package Management - Composer
Manage package dependencies.  This can streamline upgrading library files.
[Composer](http://www.getcomposer.org)
Similar to: PEAR (PHP), NuGet (.NET), NPM (NodeJS package manager), or Bower (front-end webdev)

## Unit Testing - PHPUnit
Library used to test small units of code (e.g. functions, classes). May measure coding metrics, often in conjunction with other tools.
PHPUnit - popular testing library for PHP that uses the xUnit architecture.
Similar to: NUnit (.NET), MSTest (.NET), JUnit (Java), etc.
Related to: Code Analysis (.NET Visual Studio)

## Other testing
Libraries used to test for integration (of multiple system components), functionality, and user acceptance conditions.

### Browser Testing - Selenium, BrowserStack
Library and tools to test browser interactions.
#### Selenium
A library and set of tools that allow you to programmatically control a browser.  It has bindings in multiple languages (including C# and PHP), though the most popular one is Java.

Related to: BrowserStack (extension service to test on multiple devices)
Similar to: PhantomJS (javascript), HttpUnit (Java), Watir (Ruby web testing)
#### Browserstack
A service that facilitates testing on multiple browser types, versions, and OSes (including mobile).


## Code Standards Checker - PHPCodeSniffer
Automatically check code against a set of rules/standards.
PHPCodeSniffer is a popular tool for standardizing PHP code.
Commands:
* phpcs (php code sniffer)
* phpcbf (php code beautifier and fixer)

Similar to: StyleCop (.NET), JSHint (javascript), JSLint (javascript)
Related to: Lint programs (syntax checkers)


## Documentation - phpDocumentor
Tooling to extract and format documentation from specially-formatted code comments (docblocks).
phpDocument - popular php documentation program that uses xDoc style formatting (javadoc, jsdoc, etc.). This can be downloaded as a PHP archive (.PHAR file) from http://phpdoc.org/phpDocumentor.phar





