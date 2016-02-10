# base-theme
WordPress theme for SDES Template Rev. 2015 layout.

# Table of Contents
* [Todo](#todo)
* [Virtual Machine for Local Development](#virtual-machine-for-local-development)
* [Development Toolset](#development-toolset)


# TODO
- Fix moment of creation bug for any calls to "get_option"


# Virtual Machine for Local Development
[VCCW](http://vccw.cc/) is a configuation/stack for setting up a virtual development environment with VirtualBox + Vagrant + Chef + WordPress. The virtual machine is run on [VirtualBox](https://www.virtualbox.org/). Vagrant spins up a virtual machine harddrive from a template "box", [Chef](https://www.chef.io/chef/)[1] is used for configuration management, and WordPress is already installed with all requirements/dependencies, along with a suite of tools.
Quick Install notes (see VCCW homepage for details):
1. Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads).
2. Install [Vagrant](https://www.vagrantup.com/downloads.html).
3. Download the vccw harddrive image with vagrant: `vagrant box add miya0001/vccw --box-version ">=2.19.0"` (this may take a long time -- 1.55GB+ download)
4. Create a folder for the Vagrant virtual machine (based on, for example: https://github.com/vccw-team/vccw/archive/2.19.0.zip)
5. From cmd.exe or powershell, `cd` into the directory.
6. If you wish to customize any local settings, `cp provision\default.yml site.yml` and edit site.yml.
7. `vagrant up` (initial provisioning may take several minutes).
8. Add an entry to your HOSTS file[2] for the VM's IP address[3]: `192.168.33.10 vccw.dev`
9. Clone this repository to the "www\wordpress\wp-content\themes\" folder of your vccw-x.xx.x installation. Use either GitHub for windows or `git clone https://github.com/ucf-sdes-it/base-theme.git`[4].
10. Access the WordPress install in your browser from http://vccw.dev/ or http://192.168.33.10 and develop as normal.  The following Vagrant commands may prove useful:
  - Start/Recreate VM: `vagrant up`
  - Suspend VirtualBox VM:  `vagrant suspend`
  - Resume VirtualBox VM:   `vagrant resume`
  - Shutdown VirtualBox VM: `vagrant halt`
  - Restart and reload Vagrantfile: `vagrant reload`
  - Delete VM (leaves directory from step 4 intact): `vagrant destroy` (this may take several minutes).
  Consult `vagrant help` or the [Vagrant Documentation](https://www.vagrantup.com/docs/) for additional information.

VCCW also offers another VM specifcally for [Theme Reviewing](https://github.com/vccw-team/vccw-for-theme-review).
Testing in a fresh environment could be useful after feature completion, whether for a feature branch or alpha testing.



# Development Toolset
Overview of recommended development tools for coding.

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
phpDocument - popular php documentation program that uses xDoc style formatting. This can be downloaded as a PHP archive (.PHAR file) from http://phpdoc.org/phpDocumentor.phar
Similar to: javadoc, jsdoc




--
[1]: Specifcally, [Chef Solo](https://docs.chef.io/chef_solo.html)

[2]: Hosts file on windows: c:\Windows\System32\drivers\etc\hosts (must edit as administrator).

[3]: By default, VCCW uses Virtualbox's NAT networking mode.

[4]: You may want to add an NTFS junction point that links from your c:\github folder and targets the cloned folder's location. From cmd.exe, run `mklink /j` (or using Powershell Community Extenions, `new-junction`). Creating a junction in the other direction (targeting the vccw folder) will be difficult/impossible due to Virtualbox security concerns, involving the setting ```VBoxManage.exe setextradata <VM Name> VBoxInternal2/SharedFoldersEnableSymlinksCreate/<volume> 1```.
[NTFS junction]: See https://en.wikipedia.org/wiki/NTFS_junction_point and http://www.hanselman.com/blog/MoreOnVistaReparsePoints.aspx
