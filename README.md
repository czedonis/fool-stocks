=== FMP Stock Recommendations and News ===

Contributors: czedonis
Tags: Motley Fool code Challenge
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Installation ==

Composer is present for autoloading classes. You'll need to run composer install.
After activating this plugin, you'll see 2 new Menu items on the left side for Stock recommendations and News Articles,
and a new Page template option when creating a new page. 

== Features ==
2 new CPTs + custom Taxonomy:
Stock Recommendation - has access to Ticker Symbol taxonomy which will auto populate the side bar information if info
exists for it. Title will auto populate the name of the Ticker Symbol associated with it. Has archive page.

News Article - has access to Ticker Symbol taxonomy to the same end as above, less for display purposes and more to pull
the correct articles into the company pages dynamically. 

Ticker Symbol taxonomy when populated, will be what determines the API calls made to pull in information from the 
provider. Before hitting 'refresh stock info', make sure you've added all the stocks you need to this taxonomy. These
calls will populate a custom table we will use in lieu of continual calls when pages need this information.

Company pages created using the 'company page' template can be associated with this taxonomy for symbols. And will pull 
in associated API information and related articles and stock recommendations dynamically.

Note: Taxonomy name is used to display on the front end, any way desired, but due to how the endpoint expects the stock 
symbols to be isolated, slug should be only the symbol. Ex: Name: Naxdaq:AAPL needs a slug of just AAPL.

== Description ==
I decided to isolate every aspect of this project in a plugin to allow for it to be dropped into any theme. I used a 
very generic theme working on this called generic, so it should fit into any theme style wise. There is polishing
especially around styling and front end tweaks that I would've liked to do, but I wanted to keep to the time constraint 
as much as possible. Adding an auto update cron to the API calls, or the ability to run the call on saving a new ticker 
tax value would have been nice, I didn't want to run the risk of hitting the API key limit accidentally while testing. 

