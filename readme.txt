=== VAT / UST ID Checker - Validator EU for WooCommerce ===
Contributors: mlfactory
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=EJHKYWR5M5V3N
Tags: reverse charge, woocommerce, vat, validator, ustid
Requires at least: 5.0
Tested up to: 5.5
Requires PHP: 5.2.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Just a small plugin that allows you to apply reverse charge (Europe) in WooCommerce.

== Description ==

Just a small plugin that allows you to apply reverse charge (Europe) in WooCommerce.


<h4>Features</h4>
This small plugin for <a href="https://wordpress.org/plugins/woocommerce/" target="blank">WooCommerce</a> allows you:
<ul>
<li>1.) Checks if the vat ID given by the customer is valid</li>
<li>2.) Checks if reverse charge can be applied</li>
<li>2.) If reverse charge can be applied - vat is automatically omitted</li>
</ul>

<h4>Its so simple, really!</h4>
All you have to do is to activate the plugin.
As soon as the plugin is activated, two selection buttons are automatically displayed at the checkout where the customer selects whether he is a company or a private person. If the customer chooses that he is a company he can enter his vat/ust id. Directly after entering the vat/ust id is checked in real time. The customer gets an immediate output if the vat/ust id is valid and if reverse charge can be applied.
If reverse charge can be applied, vat is automatically omitted.
The output of the API is also added to the order as a note so you can check everything in detail.

<h4>Compatible plugins</h4>
<ul>
<li>
<a href="https://ec.europa.eu" target="blank">WooCommerce PDF Invoices</a><br />
An information on the invoice regarding reverse charge is displayed as well as the vat id itself.
</li>
</ul>

<h4>Used API</h4>
The verification is done via the API of <a href="https://ec.europa.eu" target="blank">ec.europa.eu (VIES/MIAS)</a>.
All vat ids within the EU can be checked for validity.
	
	
<h3>Deutsch</h3>	

<h4>Funktionen</h4>
Dieses kleine Plugin für <a href="https://wordpress.org/plugins/woocommerce/" target="blank">WooCommerce</a> erlaubt es:
<ul>
<li>1.) Zu prüfen ob die des Kunden eingegebene UST ID gültig ist</li>
<li>2.) Zu prüfen ob eine Umkehr der Steuerschuld (§ 13b UStG) möglich ist</li>
<li>2.) Wenn eine Umkehr der Steuerschuld möglich ist entfällt die MwSt.</li>
</ul>

<h4>Es ist so einfach, wirklich!</h4>
Alles was Sie tun müssen, ist es das Plugin zu aktivieren.
Sobald das Plugin aktiviert ist, erscheinen 2 Auswahlmöglichkeiten an der Kasse (Privatperson/Firma) sowie ein Eingabefeld für die UST ID.
Der Kunde kann nun auswählen, ob er eine Privatperson ist oder eine Firma vertritt.
Wählt der Kunde Firma aus so kann er auch seine UST ID eingeben.
Sobald der Kunde seine UST ID eingibt wird diese in Echtzeit auf Ihre Gültigkeit geprüft.
Ist die UST ID gültig so wird automatisch die Steuerumkehrschuld angewendet - sprich die Mehrwertsteuern entfallen für den Leistungserbringer.

<h4>Kompatible Plugins</h4>
<ul>
<li>
<a href="https://ec.europa.eu" target="blank">WooCommerce PDF Invoices</a><br />
Eine Information bzgl. der Umkehr der Steuerschuld wird ausgegeben - ebenso die UST ID selbst.
</li>
</ul>

<h4>Genutze API</h4>
Die Prüfung erfolgt über die API von <a href="https://ec.europa.eu" target="blank">ec.europa.eu (VIES/MIAS)</a>.
Es können alle UST ID's innerhalb der EU auf Ihre Gültigkeit geprüft werden.

== Installation ==
 
1. Upload plugin over wordpress (Or install the plugin via the WordPress build in function)
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That was all. Navigate to your WooCommerce checkout page.
You should now see 2 options (company/private person) and an input field for the vat id.
 
== Frequently Asked Questions ==

 
== Screenshots ==

 
== Changelog ==
 
= 1.0.0 =
* released 

== Upgrade Notice ==