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

display_about($screen_name);

//.........................................................................................
function display_about($screen_name='') {
	
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
<h2>Terms of Use Agreement</h2>
<div class='info'>
Welcome to our Web site.  By using our site, you are agreeing to comply with and be bound by the following terms of use. 
Please review the following terms carefully. If you do not agree to these terms, you should not use this site.  
The term 'Twextra' refers to Viewista, Inc., the owner of the Web site.  
The term 'you' refers to the user or viewer of our Web Site.
</div>
<h3> Acceptance of Agreement</h3>
<div class='info'>
You agree to the terms and conditions outlined in this Terms of Use Agreement (\"Agreement\") with respect to our site 
(the \"Site\"). This Agreement constitutes the entire and only agreement between us and you, and supersedes all prior or 
contemporaneous agreements, representations, warranties and understandings with respect to the Site, the content, products 
or services provided by or through the Site, and the subject matter of this Agreement. This Agreement may be amended at any 
time by us from time to time without specific notice to you. The latest Agreement will be posted on the Site, and you should 
review this Agreement prior to using the Site.
</div>
<h3>	Copyright</h3>
<div class='info'>

The content, organization, graphics, design, compilation, magnetic translation, digital conversion and other matters related 
to the Site are protected under applicable copyrights, trademarks and other proprietary (including but not limited to 
intellectual property) rights. The copying, redistribution, use or publication by you of any such matters or any part of the 
Site, except as allowed by Section \"Limited License; Permitted Uses\" below, is strictly prohibited. You do not acquire 
ownership rights to any content, document or other materials viewed through the Site. The posting of information or materials 
on the Site does not constitute a waiver of any right in such information and materials.  Some of the content on the site is 
the copyrighted work of third parties.
</div>
<h3>	Service Marks</h3>
<div class='info'>
\"twextra.com\" and others are our service marks or registered service marks or trademarks.  Other product and company names 
mentioned on the Site may be trademarks of their respective owners.
</div>
<h3>	Limited License; Permitted Uses</h3>
<div class='info'>
You are granted a non-exclusive, non-transferable, revocable license (a) to access and use the Site strictly in accordance with 
this Agreement; (b) to use the Site solely for internal, personal, non-commercial purposes; and (c) to print out discrete 
information from the Site solely for internal, personal, non-commercial purposes and provided that you maintain all copyright 
and other policies contained therein.  No print out or electronic version of any part of the Site or its contents may be used 
by you in any litigation or arbitration matter whatsoever under any circumstances.
</div>
<h3>	Restrictions and Prohibitions on Use</h3>

<div class='info'>
Your license for access and use of the Site and any information, materials or documents (collectively defined as 'Content 
and Materials') therein are subject to the following restrictions and prohibitions on use:  You may not (a) copy, print 
(except for the express limited purpose permitted by Section 4 above), republish, display, distribute, transmit, sell, rent, 
lease, loan or otherwise make available in any form or by any means all or any portion of the Site or any Content and Materials 
retrieved therefrom; (b) use the Site or any materials obtained from the Site to develop, of as a component of, any information, 
storage and retrieval system, database, information base, or similar resource (in any media now existing or hereafter developed), 
that is offered for commercial distribution of any kind, including through sale, license, lease, rental, subscription, or any 
other commercial  distribution mechanism; (c) create compilations or derivative works of any Content and Materials from the Site; 
(d) use any Content and Materials from the Site in any manner that may infringe any copyright, intellectual property right, 
proprietary right, or property right of us or any third parties; (e) remove, change or obscure any copyright notice or other 
proprietary notice or terms of use contained in the Site; (f) make any portion of the Site available through any timesharing 
system, service bureau, the Internet or any other technology now existing or developed in the future; (g) remove, decompile, 
disassemble or reverse engineer any Site software or use any network monitoring or discovery software to determine the Site 
architecture; (h) use any automatic or manual process to harvest information from the Site; (i) use the Site for the purpose of 
gathering information for or transmitting (1) unsolicited commercial email; (2) email that makes use of headers, invalid or 
nonexistent domain names, or other means of deceptive addressing; and (3) unsolicited telephone calls or facsimile transmissions; 
(j) use the Site in a manner that violates any state or federal law regulating email, facsimile transmissions or telephone 
solicitations; and (k) export or re-export the Site or any portion thereof, or any software available on or through the Site, 
in violation of the export control laws or regulations of the United States.
</div>
<h3>	Forms, Agreements & Documents</h3>
<div class='info'>
We may make available through the Site or through other Web sites sample and actual forms, checklists, business documents and 
legal documents (collectively, 'Documents').  All Documents are provided on a non-exclusive license basis only for your 
personal one-time use for non-commercial purposes, without any right to re-license, sublicense, distribute, assign or transfer 
such license.  Documents are provided for a charge and without any representations  or warranties, express or implied, as to 
their suitability, legal effect, completeness, currentness, accuracy, and/or appropriateness.  THE DOCUMENTS ARE PROVIDED 'AS 
IS', 'AS AVAILABLE', AND WITH 'ALL FAULTS', AND WE AND ANY PROVIDER OF THE DOCUMENTS DISCLAIM ANY WARRANTIES, 
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.  The Documents may be 
inappropriate for your particular circumstances.  Furthermore, state laws may require different or additional provisions to 
ensure the desired result.  You should consult with legal counsel to determine the appropriate legal or business documents 
necessary for your particular transactions, as the Documents are only samples and may not be applicable to a particular 
situation.  Some Documents are public domain forms or available from public records.
</div>
<h3>	No Legal Advice or Attorney-Client Relationship</h3>
<div class='info'>
Information contained on or made available through the Site is not intended to and does not constitute legal advice, 
recommendations, mediation or counseling under any circumstance and no attorney-client relationship is formed.  We do not 
warrant or guarantee the accurateness, completeness, adequacy or currency of the information contained in or linked to the Site.  
Your use of information  on the Site or materials linked to the Site is entirely at your own risk.  We are not a law firm and 
the Site is not a lawyer referral service.

</div>
<h3>	Linking to the Site</h3>
<div class='info'>
You may provide links to the Site, provided (a) that you do not remove or obscure, by framing or otherwise, advertisements, 
the copyright notice, or other notices on the Site, (b) your site does not engage in illegal or pornographic activities, and 
(c) you discontinue providing links to the Site immediately upon request by us.
</div>
<h3>	Advertisers</h3>
The Site may contain advertising and sponsorships. Advertisers and sponsors are responsible for ensuring that material 
submitted for inclusion on the Site is accurate and complies with applicable laws.  We are not responsible for the illegality 
or any error, inaccuracy or problem in the advertiser's or sponsor's materials.

<h3>	Registration</h3>
<div class='info'>
Certain sections of, or offerings from, the Site may require you to register.  If registration is requested, you agree to 
provide us with accurate, complete registration information.  Your registration must be done using your real email address 
and accurate information.  Each registration is for your personal use only and not on behalf of any other person or entity. 
We do not permit (a) any other person using the registered sections under your name; or (b) access through a single name 
being made available to multiple users on a network.  You are responsible for preventing such unauthorized use.

</div>
<h3>	Errors, Corrections and Changes</h3>
<div class='info'>
We do not represent or warrant that the Site will be error-free, free of viruses or other harmful components, or that 
defects will be corrected.  We do not represent or warrant that the information available on or through the Site will be 
correct, accurate, timely or otherwise reliable.  We may make changes to the features, functionality or content of the Site 
at any time.  We reserve the right in our sole discretion to edit or delete any documents, information or other content 
appearing on the Site.
</div>
<h3>	Third Party Content</h3>
<div class='info'>
Our site provides access to Third party content.  We are not responsible for and assume no liability for any mistakes, 
misstatements of law, defamation, omissions, falsehood, obscenity, pornography or profanity in the statements, opinions, 
representations or any other form of content on the Site.  You understand that the information and opinions in the third 
party content represent solely the thoughts of the author and is neither endorsed by nor does it necessarily reflect our belief.
</div>
<h3>	Unlawful Activity</h3>

<div class='info'>
We reserve the right to investigate complaints or reported violations of this Agreement and to take any action we deem 
appropriate, including but not limited to reporting any suspected unlawful activity to law enforcement officials, regulators, 
or other third parties and disclosing any information necessary or appropriate to such persons or entities relating to your 
profile, email addresses, usage history, posted materials, IP addresses and traffic information.
</div>
<h3>	Indemnification</h3>
<div class='info'>
You agree to indemnify, defend and hold us and our partners, agents, officers, directors, employees, subcontractors, 
successors, assigns, third party suppliers of information and documents, attorneys, advertisers, product and service 
providers, and affiliates (collectively, \"Affiliated Parties\") harmless from any liability, loss, claim and expense, 
including reasonable attorney's fees, related to your violation of this Agreement or use of the Site. 
</div>
<h3>	Nontransferable</h3>
<div class='info'>
Your right to use the Site is not transferable or assignable. Any password or right given to you to obtain information or 
documents is not transferable or assignable.
</div>
<h3>	Disclaimer</h3>

<div class='info'>
THE INFORMATION, CONTENT AND DOCUMENTS FROM OR THROUGH THE SITE ARE PROVIDED \"AS-IS,\" \"AS AVAILABLE,\" WITH 'ALL FAULTS', 
AND ALL WARRANTIES, EXPRESS OR IMPLIED, ARE DISCLAIMED (INCLUDING BUT NOT LIMITED TO THE DISCLAIMER OF ANY IMPLIED WARRANTIES 
OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE). THE INFORMATION AND SERVICES MAY CONTAIN BUGS, ERRORS, PROBLEMS OR 
OTHER LIMITATIONS. WE AND OUR AFFILIATED PARTIES HAVE NO LIABILITY WHATSOEVER FOR YOUR USE OF ANY INFORMATION OR SERVICE, 
EXCEPT AS PROVIDED IN SECTION 17(b). IN PARTICULAR, BUT NOT AS A LIMITATION THEREOF, WE AND OUR AFFILIATED PARTIES ARE NOT 
LIABLE FOR ANY INDIRECT, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES (INCLUDING DAMAGES FOR LOSS OF BUSINESS, LOSS OF PROFITS, 
LITIGATION, OR THE LIKE), WHETHER BASED ON BREACH OF CONTRACT, BREACH OF WARRANTY, TORT (INCLUDING NEGLIGENCE), PRODUCT 
LIABILITY OR OTHERWISE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. THE NEGATION AND LIMITATION OF DAMAGES SET FORTH 
ABOVE ARE FUNDAMENTAL ELEMENTS OF THE BASIS OF THE BARGAIN BETWEEN US AND YOU. THIS SITE AND THE PRODUCTS, SERVICES, DOCUMENTS 
AND INFORMATION PRESENTED WOULD NOT BE PROVIDED WITHOUT SUCH LIMITATIONS. NO ADVICE OR INFORMATION, WHETHER ORAL OR WRITTEN, 
OBTAINED BY YOU FROM US THROUGH THE SITE OR OTHERWISE SHALL CREATE ANY WARRANTY, REPRESENTATION OR GUARANTEE NOT EXPRESSLY 
STATED IN THIS AGREEMENT. 

ALL RESPONSIBILITY OR LIABILITY FOR ANY DAMAGES CAUSED BY VIRUSES CONTAINED WITHIN THE ELECTRONIC FILE CONTAINING A FORM OR 
DOCUMENT IS DISCLAIMED. 
</div>
<h3>	Limitation of Liability</h3>
<div class='info'>
	(a)	We and any Affiliated Party shall not be liable for any loss, injury, claim, liability, or damage of any kind 
	resulting in any way from (a) any errors in or omissions from the Site or any  services or products obtainable therefrom, 
	(b) the unavailability or interruption of the Site or any features thereof, (c) your use of the Site, (d) the content 
	contained on the Site, or (e) any delay or failure in performance beyond the control of a Covered Party.

(b)	THE AGGREGATE LIABILITY OF US AND THE AFFILIATED PARTIES IN CONNECTION WITH ANY CLAIM ARISING OUT OF OR RELATING TO THE 
SITE AND/OR THE PRODUCTS, INFORMATION, DOCUMENTS AND SERVICES PROVIDED HEREIN OR HEREBY SHALL NOT EXCEED $100 AND THAT AMOUNT 
SHALL BE IN LIEU OF ALL OTHER REMEDIES WHICH YOU MAY HAVE AGAINST US AND ANY AFFILIATED PARTY.
</div>
<h3>	Use of Information</h3>
<div class='info'>
We reserve the right, and you authorize us, to the use and assignment of all information regarding Site uses by you and all 
information provided by you in any manner consistent with our Privacy Policy. All remarks, suggestions, ideas, graphics, or 
other information communicated by you to us (collectively, a \"Submission\") will forever be our property. We will not be 
required to treat any Submission as confidential, and will not be liable for any ideas (including without limitation, product, 
service or advertising ideas) and will not incur any liability as a result of any similarities that may appear in our future 
products, services or operations. Without limitation, we will have exclusive ownership of all present and future existing rights 
to the Submission of every kind and nature everywhere. We will be entitled to use the Submission for any commercial or other 
purpose whatsoever, without compensation to you or any other person sending the Submission. You acknowledge that you are 
responsible for whatever material you submit, and you, not us, have full responsibility for the message, including its 
legality, reliability, appropriateness, originality, and copyright.
</div>
<h3>	Third-Party Services</h3>

<div class='info'>
We may allow access to or advertise certain third-party product or service providers (\"Merchants\") from which you may 
purchase certain goods or services. You understand that we do not operate or control the products or services offered by 
Merchants. Merchants are responsible for all aspects of order processing, fulfillment, billing and customer service. We are 
not a party to the transactions entered into between you and Merchants. You agree that use of or purchase from such Merchants 
is AT YOUR SOLE RISK AND IS WITHOUT WARRANTIES OF ANY KIND BY US, EXPRESSED, IMPLIED OR OTHERWISE INCLUDING WARRANTIES OF TITLE, 
FITNESS FOR PURPOSE, MERCHANTABILITY OR NON-INFRINGEMENT. UNDER NO CIRCUMSTANCES ARE WE LIABLE FOR ANY DAMAGES ARISING FROM THE 
TRANSACTIONS BETWEEN YOU AND MERCHANTS OR FOR ANY INFORMATION APPEARING ON MERCHANT SITES OR ANY OTHER SITE LINKED TO OUR SITE.
</div>
<h3>	Third-Party Merchant Policies</h3>
<div class='info'>
All rules, policies (including privacy policies) and operating procedures of Merchants will apply to you while on any Merchant 
sites. We are not responsible for information provided by you to Merchants. We and the Merchants are independent contractors 
and neither party has authority to make any representations or commitments on behalf of the other.
</div>
<h3>	Privacy Policy</h3>
<div class='info'>
Our Privacy Policy, as it may change from time to time, is a part of this Agreement.  You must review this Privacy Policy by 
clicking on<a href=\"privacy.php\"> this</a> link.

</div>
<h3>	Payments</h3>
<div class='info'>
You represent and warrant that if you are purchasing something from us or from Merchants that (i) any credit information you 
supply is true and complete, (ii) charges incurred by you will be honored by your credit card company, and (iii) you will pay 
the charges incurred by you at the posted prices, including any applicable taxes.
</div>
<h3>	Securities Laws</h3>
<div class='info'>
The Site may include statements concerning our operations, prospects, strategies, financial condition, future economic 
performance and demand for our products or services, as well as our intentions, plans and objectives (particularly with 
respect to product and service offerings), that are forward-looking statements. These statements are based upon a number of 
assumptions and estimates which are subject to significant uncertainties, many of which are beyond our control. When used on 
our Site, words like \"anticipates,\" \"expects,\" \"believes,\" \"estimates,\" \"seeks,\" \"plans,\" \"intends,\" \"will\" 
and similar expressions are intended to identify forward-looking statements designed to fall within securities law safe 
harbors for forward-looking statements. The Site and the information contained herein does not constitute an offer or a 
solicitation of an offer for sale of any securities. None of the information contained herein is intended to be, and shall 
not be deemed to be, incorporated into any of our securities-related filings or documents.
</div>
<h3>	Links to other Web Sites</h3>
<div class='info'>

The Site contains links to other Web sites. We are not responsible for the content, accuracy or opinions expressed in such Web 
sites, and such Web sites are not investigated, monitored or checked for accuracy or completeness by us. Inclusion of any linked 
Web site on our Site does not imply approval or endorsement of the linked Web site by us. If you decide to leave our Site and 
access these third-party sites, you do so at your own risk.
</div>

<h3>	Information and Press Releases</h3>
<div class='info'>
The Site contains information and press releases about us. We disclaim any duty or obligation to update this information or 
any press releases. Information about companies other than ours contained in the press release or otherwise, should not be 
relied upon as being provided or endorsed by us.
</div>
<h3>	Legal Compliance</h3>
<div class='info'>
You agree to comply with all applicable domestic and international laws, statutes, ordinances and regulations regarding your 
use of the Site and the Content and Materials provided therein.
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