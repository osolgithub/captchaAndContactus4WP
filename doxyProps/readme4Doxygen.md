# README

This documentation is in **doxyProps/readme4Doxygen.md** folder
<!-- Replace ^[^#]([\r\n]*) with blank to make a template. In note pad you can also use `negative lookahead` ^(?!") -->

## Author

Sreekanth Dayanand <br />
[Outsource Online Internet Solutions](http://www.outsource-online.net/)

## Contributors

This is a solo project.


## Synopsis

Plugin to add captcha to core wordpress forms and additional option for contact us page with ajax captcha verification.

## Description

### Plugin to add captcha to core wordpress forms and additional option for contact us page.

**Captcha Inserted Forms are:**
1. Login
2. Forgot Password
3. Register
4. Comments
**Contactus Form** 
Insert the code [cccontact] in a page where you want to show contact us form. 

**MVC Structure**
1. Main file `custCaptchaContact.php` only contains calls to wp hooks
2. As a special case in wordpress `hooks` functionalities are handled by `Hooks` classes
3. Rest of functionality is handled by Model , View & Controller Classes, along with some helper & extra classes

**Multi Language Option**
Enabled with 
```
load_plugin_textdomain('osolwpccc', false, dirname( plugin_basename(__FILE__)).'/languages');
```
** AJAX usage**
Ajax isused via `add_action('wp_ajax_nopriv_` & `add_action('wp_ajax_`

***Autoloading in PHP***
Autoloading is enabled with *spl_autoload_register* function in index.php

### Use Case Diagrams

UML diagrams are stored in `documentation/UMLDiagrams` folder

## Prerequisites

1. PHP version 5.1.0+, ideal is 7+
2. PHP GD Library should be enabled


## Installation

1. Save the files in a  folder in wp-content/plugins/ folder
2. Install from admin Plugins &gt; &gt; `activate` (under Customizable Captcha and contact us) 
3. if you choose `GDPR Compliant(No Cookie)` in Captcha settings, it advised to change `Encryption Key` replacing the default `YourUniqueEncryptionKey` 
## Extending / Installing Addons

Any suggetions are welcome

## Contributing

Will come soon in github

## License / Copyright Info

This project is released under the GNU Public License.

## Citation
the following will shortly come 
1. How this software can be cited
2. DOI(Digital Object Identifier) link/image

## Contact

[Contact Us](https://outsource-online.net/contact-us.html)