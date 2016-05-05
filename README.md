# base-theme
WordPress theme for SDES Template Rev. 2015 layout.

# Table of Contents
* [Todo](#todo)
* [Virtual Machine for Local Development](#virtual-machine-for-local-development)
* [Development Toolset](#development-toolset)


# TODO
- Fix moment of creation bug for any calls to "get_option"


# Virtual Machine for Local Development
[VCCW](http://vccw.cc/) is a configuation/stack for setting up a virtual development environment with Vagrant + VirtualBox + CentOS + Chef + WordPress.
* [Vagrant](https://www.vagrantup.com/) spins up a virtual machine harddrive from a template "box".
* [VirtualBox](https://www.virtualbox.org/) runs the virtual machine.
* [CentOS](https://www.centos.org/) is a redhat compatible Linux distro.
* [Chef](https://www.chef.io/chef/)<sup id="a1">[1](#fn1)</sup> is used for configuration management.
* [WordPress](https://wordpress.org/) is already installed with all requirements/dependencies, along with a suite of development tools, including [WP-CLI](http://wp-cli.org/), [PHP Composer](https://getcomposer.org/), and [PHPUnit](https://phpunit.de/).

Quick Install notes (see [VCCW homepage](http://vccw.cc/) for more details):

1. Install [VirtualBox](https://www.virtualbox.org/wiki/Downloads). The installer may temporarily disable the network and/or require a restart.
2. Install [Vagrant](https://www.vagrantup.com/downloads.html). This may require a restart (adds to $env:PATH).
3. Download the vccw harddrive image with vagrant: `vagrant box add miya0001/vccw --box-version ">=2.19.0"` (this may take a long time -- 1.55GB+ download)
4. Create a folder for the Vagrant virtual machine (based on, for example: https://github.com/vccw-team/vccw/archive/2.20.0.zip)
5. From cmd.exe or powershell, `cd` into the directory.
6. Make a site.yml file in the Vagrant directory via: `cp provision\default.yml site.yml` and edit the following values:
   ```
   multisite: true
   plugins:
     - dynamic-hostname
     - wp-total-hacks
     - tinymce-templates
     - what-the-file
     - wordpress-mu-domain-mapping
   ```
7. `vagrant up` (initial provisioning may take several minutes).
8. Add an entry to your HOSTS file<sup id="a2">[2](#fn2)</sup>. for the VM's IP address<sup id="a3">[3](#fn3)</sup>.: `192.168.33.10 vccw.dev`
9. Clone this repository to the "www\wordpress\wp-content\themes\" folder of your vccw-x.xx.x installation. Use either GitHub for windows or `git clone https://github.com/ucf-sdes-it/base-theme.git`<sup id="a4">[4](#fn4)</sup>.
10. Access the WordPress install in your browser from http://vccw.dev/ or http://192.168.33.10 and develop as normal.  The following Vagrant commands may prove useful:
  - Start/Recreate VM: `vagrant up`
  - Suspend VirtualBox VM:  `vagrant suspend`
  - Resume VirtualBox VM:   `vagrant resume`
  - Shutdown VirtualBox VM: `vagrant halt`
  - Restart and reload Vagrantfile: `vagrant reload`
  - Delete VM (leaves directory from step 4 intact): `vagrant destroy` (this may take several minutes).
  Consult `vagrant help` or the [Vagrant Documentation](https://www.vagrantup.com/docs/) for additional information.
11. Remember to "Network Activate" the theme from http://vccw.dev/wp-admin/network/themes.php

Optional Installation Steps
1. To use PHPDoc:
  - Install PHPDoc on your system with: `composer global require phpdocumentor/phpdocumentor=2.8.*`
  - If [GraphViz](http://graphviz.org/Download..php) is not installed on your system, it needs to be installed (tested with [graphviz-2.38.msi](http://graphviz.org/Download_windows.php) on Windows 8).
  - Make sure to add the GraphViz bin folder (`C:\Program Files (x86)\Graphviz2.38\bin`) to your PATH.
  - Run `composer phpdoc:all` to compile the `docs` folder (this make take a few minutes the first time it is run).
2. Optionally, install [vagrant-multi-putty](https://github.com/nickryand/vagrant-multi-putty) with `vagrant plugin install vagrant-multi-putty`.  This enables the command `vagrant putty` to open an SSH session using PuTTY<sup id="a5">[5](#fn5)</sup>.

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


## Documentation Generator - phpDocumentor
Tooling to extract and format documentation from specially-formatted code comments (docblocks).
phpDocument - popular php documentation program that uses xDoc style formatting.
This can be downloaded as a PHP archive (.PHAR file) from http://phpdoc.org/phpDocumentor.phar or installed systemwide using: `composer global require phpdocumentor/phpdocumentor=2.8.*`.
NOTE: PHPDocumentor requires [GraphViz](http://graphviz.org/Download..php) to be installed on your system: http://graphviz.org/Download_windows.php (tested with graphviz-2.38.msi on Windows 8).
Make sure to add `C:\Program Files (x86)\Graphviz2.38\bin` to your PATH. An update adding phpdoc to VCCW is currently pending.dot
Similar to: javadoc, jsdoc
Alternatives: phpDox, Sami, Doxygen, Apigen. (Switching or supplementing with these might make sense, e.g., phpDox includes code metrics, Sami generates an index view).




--
<a id="fn1"/>[^1](#a1): Specifcally, [Chef Solo](https://docs.chef.io/chef_solo.html)

<a id="fn2"/>[^2](#a2): Hosts file on windows: c:\Windows\System32\drivers\etc\hosts (must edit as administrator).

<a id="fn3"/>[^3](#a3)</span>: By default, VCCW uses Virtualbox's [NAT networking mode](http://www.virtualbox.org/manual/ch06.html#network_nat) for Adapter 1 and [Host-only networking](http://www.virtualbox.org/manual/ch06.html#network_hostonly) for Adapter 2.

<a id="fn4"/>[^4](#a4): You may want to add an NTFS junction point* that links from your c:\github folder and targets the cloned folder's location. From cmd.exe, run `mklink /j` (or using Powershell Community Extenions, `new-junction`). Creating a junction in the other direction (targeting the vccw folder) will be difficult/impossible due to Virtualbox security concerns, involving the setting ```VBoxManage.exe setextradata <VM Name> VBoxInternal2/SharedFoldersEnableSymlinksCreate/<volume> 1```.

<a id="fn5"/>[^5](#a5): [PuTTY](http://www.chiark.greenend.org.uk/~sgtatham/putty/download.html) is a windows and *nix client for SSH, telnet, and rlogin. To use the VM's private key instead of a password every time, follow [these instructions](https://github.com/nickryand/vagrant-multi-putty#ssh-private-key-conversion-using-puttygen) with `vccw-x.xx.x\.vagrant\machines\vccw.dev\virtualbox\private_key`.

*[NTFS junction point]: See https://en.wikipedia.org/wiki/NTFS_junction_point and http://www.hanselman.com/blog/MoreOnVistaReparsePoints.aspx
