<?php
if (session_id () == "") session_start ();
$docroot = $_SERVER ['DOCUMENT_ROOT'];
require_once $docroot . "/config.php";
require_once $docroot . "/models/twextra_model.php";
require_once $docroot . "/controllers/twextra_controller.php";
require_once $docroot . "/banner.php";
require_once $docroot . "/header_html.php";

$screen_name = $_SESSION['user'];

require_once $docroot . "/tw.lib.php";

//validate access;
validate_access_twetest();

display_privacy($screen_name);

//.........................................................................................
function display_privacy($screen_name='') {
	
	//configuration parameters:
	$config_params = Config::getConfigParams ();
	$css = $config_params ['css'];
	$tweet_size_max = $config_params ['tweet_size_max'];
	$tweet_size_max_google = $config_params ['tweet_size_max_google'];
	$hostname = $config_params ['hostname'];
	$doctype = $config_params ['doctype'];
	$html_attribute = $config_params ['html_attribute'];
	$banner = banner('', 'banner'); //(user, banner_class)
	$footer = $config_params ['footer']; //
	$docroot = $config_params ['docroot'];
	$godaddy_analytics = $config_params ['godaddy_analytics'];
	$debug = $config_params ['debug'];
	$enable_stats = $config_params ['enable_stats'];	
	
	$script_path = __FUNCTION__;
	
	//save logs
	if ($enable_stats) {
		$model = new TwextraModel (); //
		$model->saveStat ();
	}
	
	$header = header_html ( $prefix ); //
	//..........................................................

	header ( "Pragma: no-cache" );
	header ( "cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
	header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past

	$message = '';
	$message .= $doctype;
	$message .= "<html $html_attribute >\n";
	
	$message .= $header;
	
	$message .= "<body>\n";
	
	$message .= "<div class='p5_wrapper'>\n";
	$message .= "<div class='p5_page'>\n";
	
	$message .= $banner;  
	
	$message .= "<div style='margin-left:auto;margin-right:auto;margin-bottom:20px;width:768px;font-size:0.8em;' >\n"; //p5_main
$message .= "<div class='info2'>
<h2>Privacy Policy</h2>
<div class='info'>
Our Privacy Policy is designed to assist you in understanding how we collect and use the personal information you provide to us and to assist you in making informed decisions when using our site and our products and services.
</div>
<h3>What Information Do We Collect?</h3>
<div class='info'>
When you visit our Web site you may provide us with two types of information: personal information you knowingly choose to disclose that is collected on an individual basis and Web site use information collected on an aggregate basis as you and others browse our Web site.
</div>
<h3>  1.	Personal Information You Choose to Provide</h3>

<h4>Registration Information</h4>

<div class='info'>
You will provide us information about yourself when you register to be a member of twextra.com, register for certain services, or register for email newsletters and alerts. You may also provide additional comments on how you see twextra.com servicing your needs and interests.
</div>
<h4>  Email Information</h4>
<div class='info'>
If you choose to correspond with us through email, we may retain the content of your email messages together with your email address and our responses. 
</div>
<h3>  2.	Web Site Use Information</h3>
<div class='info'>
Similar to other commercial Web sites, our Web site utilizes a standard technology called \"cookies\" (see explanation below, \"What Are Cookies?\") and Web server logs to collect information about how our Web site is used. Information gathered through cookies and Web server logs may include the date and time of visits, the pages viewed, time spent at our Web site, and the Web sites visited just before and just after our Web site.  We, our advertisers and ad serving companies may also use small technology or pieces of code to determine which advertisements and promotions users have seen and how users responded to them.
</div>
<h3>How Do We Use the Information That You Provide to Us?</h3>

<div class='info'>
Broadly speaking, we use personal information for purposes of administering and expanding our business activities, providing customer service and making available other products and services to our customers and prospective customers. Occasionally, we may also use the information we collect to notify you about important changes to our Web site, new services and special offers we think you will find valuable. You may notify us at any time if you do not wish to receive these offers by emailing us at contact@twextra.com.
</div>
<h3>   What Are Cookies?</h3>
<div class='info'>
A cookie is a very small text document, which often includes an anonymous unique identifier. When you visit a Web site, that site's computer asks your computer for permission to store this file in a part of your hard drive specifically designated for cookies. Each Web site can send its own cookie to your browser if your browser's preferences allow it, but (to protect your privacy) your browser only permits a Web site to access the cookies it has already sent to you, not the cookies sent to you by other sites.  Some of our business partners (e.g., advertisers) use cookies that originate from their sites. We have no access or control over those cookies.  
</div>

<h3>   How Do We Use Information We Collect from Cookies?</h3>
<div class='info'>
As you use our Web site, the site uses its cookies to differentiate you from other users. In some cases, we also use cookies to prevent you from logging in more than is necessary for security. Cookies, in conjunction with our Web server's log files, allow us to calculate the aggregate number of people visiting our Web site and which parts of the site are most popular. This helps us gather feedback in order to constantly improve our Web site and better serve our customers.
</div>
<h3>   Sharing Information with Third Parties</h3>

<div class='info'>
We may enter into alliances, partnerships or other business arrangements with third parties who may be given access to personal information including your name, address, telephone number and email for the purpose of providing you information regarding products and services that we think will be of interest to you. In connection with alliances, partnerships or arrangements, we may also provide certain information to third parties if we have determined that the information will be used in a responsible manner by a responsible third party. For example, some of our partners operate stores or provide services on our site, while others power offerings developed by us for your use. We may also use third parties to facilitate our business, including, but not limited to, sending email and processing credit card payments. In connection with these offerings and business operations, our partners and other third parties may have access to your personal information for use in connection with business activities. As we develop our business, we may buy or sell assets or business offerings. Customer, email, and visitor information is generally one of the transferred business assets in these types of transactions. We may also transfer such information in the course of corporate divestitures, mergers, or any dissolution.
</div>
<h3>   Notice of New Services and Changes</h3>
<div class='info'>
Occasionally, we may also use the information we collect to notify you about important changes to our Web site, new services and special offers we think you will find valuable. As our customer, you will be given the opportunity to notify us of your desire not to receive these offers by sending us an email request at contact@twextra.com.
</div>
<h3>  How Do We Protect Your Information? How Do We Secure Information Transmissions?</h3>
<div class='info'>
Email is not recognized as a secure medium of communication. For this reason, we request that you do not send private information to us by email. Some of the information you may enter on our Web site may be transmitted securely via Secure Sockets Layer SSL encryption services. Pages utilizing this technology will have URLs that start with HTTPS instead of HTTP. Please contact contact@twextra.com if you have any questions or concerns.
</div>
<h3>  How Can You Access and Correct Your Information?</h3>

<div class='info'>
You may request access to all your personally identifiable information that we collect online and maintain in our database by emailing 
contact@twextra.com.
</div>
<h3>  Certain Disclosures</h3>
<div class='info'>
We may disclose your personal information if required to do so by law or subpoena or if we believe that such action is necessary to (a) conform to the law or comply with legal process served on us or affiliated parties; (b) protect and defend our rights and property, our site, the users of our site, and/or our affiliated parties; (c) act under circumstances to protect the safety of users of our site, us, or third parties.
</div>
<h3>  What About Other Web Sites Linked to Our Web Site?</h3>
<div class='info'>
We are not responsible for the practices employed by Web sites linked to or from our Web site nor the information or content contained therein. Often links to other Web sites are provided solely as pointers to information on topics that may be useful to the users of our Web site. 
Please remember that when you use a link to go from our Web site to another Web site, our Privacy Policy is no longer in effect. Your browsing and interaction on any other Web site, including Web sites which have a link on our Web site, is subject to that Web site's own rules and policies. Please read over those rules and policies before proceeding.
</div>
<h3>  Your Consent</h3>

<div class='info'>
By using our Web site you consent to our collection and use of your personal information as described in this Privacy Policy. If we change our privacy policies and procedures, we will post those changes on our Web site to keep you aware of what information we collect, how we use it and under what circumstances we may disclose it.
</div>
</div>
";
	$message .= "<br style='clear:both;' />";
	
	$message .= "</div>\n"; //p5_main

	$message .= $footer;
	$message .= "</div>\n"; //page
	$message .= "</div>\n"; //wrapper

	$message .= $godaddy_analytics;
	$message .= "</body>\n</html>\n";
	
	echo $message;
	
} 


?>