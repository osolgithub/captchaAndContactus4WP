# README

This documentation is in **doxyProps/readme4Doxygen.md** folder
<!-- Replace ^[^#]([\r\n]*) with blank to make a template. In note pad you can also use `negative lookahead` ^(?!") -->

## Author

Sreekanth Dayanand <br />
[Outsource Online Internet Solutions](http://www.outsource-online.net/)

## Contributors

This is a solo project.
	
### Date 

5th January 2023

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

### UML Diagrams

[UML](https://www.javatpoint.com/uml) [diagrams](https://www.javatpoint.com/uml-diagrams) are stored in `documentation/UMLDiagrams` folder@n
Commonly used UML diagrams for small projects are

1. [Use Case Diagram](https://plantuml.com/use-case-diagram)
2. [Sequence Diagram](https://plantuml.com/sequence-diagram)
3. Extended Use Case Diagram
4. [Activity Diagram](https://plantuml.com/activity-diagram-beta). This is Different from flow chart
5. [Class Diagram](https://plantuml.com/class-diagram)
6. [Collaboration Diagram](https://www.javatpoint.com/uml-collaboration-diagram)

PS: Additionally [Flow charts](https://github.com/adrai/flowchart.js) also must be used for complex class methods

use [Plant UML](https://plantuml.com/) for generating UMLs@n
Eg: 
<table>
	<tr>
		<td valign="top">
			**Use Case Diagram**@n

			\image html doxyProps/UMLDiagrams/images/wp_captcha_contact_plugin_1_use_case.png
			
		</td>
		<td>
		**Flow chart for Captcha in WP Page**@n
		\image html doxyProps/UMLDiagrams/images/flowChartCaptchaInWPPages.png
		</td>
	</tr>
</table>



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

## References

[Work flow](http://www.outsource-online.net/blog/demos/doxygenComments/)@n
[Documentation from scratch](http://www.outsource-online.net/blog/2022/10/17/documentation-from-scratch/)@n
[Documentation steps](http://www.outsource-online.net/blog/2022/07/13/doxygen-basics/)@n
[Wordpress plugin development and related steps](http://www.outsource-online.net/blog/2022/07/02/developing-wordpress-plugin/)@n
[Git HTML Pages](http://www.outsource-online.net/blog/2022/06/13/git-command-line-tutorials/#git_html_pages)@n