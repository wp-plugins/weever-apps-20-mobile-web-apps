=== Plugin Name ===
Contributors: weeverapps
Donate link: http://weeverapps.com/
Tags: AJAX, android, apple, blackberry, weever, weaver, HTML5, iphone, ipod, mac, mobile, smartphone, theme, mobile, web app
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 2.1.7

Weever Apps: Turn your site into a true HTML5 'web app' for iPhone, Android and Blackberry - weeverapps.com

== Description ==

Weever is a service that turns your WordPress site into a mobile web app for iPhone, Blackberry Touch, Android and iPad.  Or, use our service to convert the HTML5 web app into a native app for Apple or Android.

Weever functions and feels like a native iOS, Android, or Blackberry app, except with no App Store barriers!

This plugin enables you to build and manage your app entirely within WordPress' administrator backend, utilizing best practices to present your content for a mobile-specific context. App users will be able to quickly and easy find your latest news, follow your social network feeds, touch to make a phone call or email you, watch your videos, browse your photo feeds, and more.

Setting up a Weever App is extremely easy. All you do is:

- Sign up for an subscription key at http://weeverapps.com/
- Install the plugin
- Paste in the API key
- Start adding content and branding to your app!

The plugin will forward the devices you specify to your app, and automatically provides the app with the most up-to-date info, so you do not have to manage both your app and your site.

Currently supports:

- Blog content from pages, categories, tags, and custom taxonomy
- Creation of a landing page or a slide-show using Wordpress page content
- Contact information
- Social: Twitter, Facebook, Identi.ca
- Video: Youtube, Vimeo
- Photo: Flickr, Picasa, Facebook Albums, Foursquare Venue Photos
- Events: Google Calendar, Facebook Events
- Forms through Wufoo
- Maps using geolocation stored in posts (using the Wordpress mobile apps for iPhone, Android and Blackberry, or the Geolocation plugin)
- App works for iPhone/iPod/iPad, Android devices, Blackberry touch devices, with further compatibilities coming soon.

Additional Features:

- Sandbox Mode: allows developers to play around with new layouts or work on an app for a new version of a site without needing another API key and without affecting a Live app.
- QR Codes: This extension will generate QR codes both for quick previewing of your app as you're building it, and for promoting your app publicly.

== Installation ==

1. Install directly from the Wordpress admin (search for 'Weever Apps' from the Plugins / Add New page), or upload the entire contents of the zip to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure the plugin by entering your subscription key
1. Add the content you wish to appear in your mobile app from the 'App Features + Navigation' screen - simply click a tab, select the content you want to appear, and submit!
1. Scan the QR code with your phone to preview your app
1. Turn your app online by clicking the icon in the top-right corner in the 'Weever Apps' screen

You can obtain a subscription key at http://weeverapps.com/

== Frequently Asked Questions ==

= How do I start creating my mobile app? =

1. Install the plugin
1. Sign up for a subscription key at http://weeverapps.com/
1. Add the content you wish to appear in your mobile app from the 'App Features + Navigation' screen - simply click a tab, select the content you want to appear, and submit!
1. Scan the QR code with your phone to preview your app
1. Turn your app online by clicking the icon in the top-right corner in the 'Weever Apps' screen

That's it!

= Can I customize the look and feel of the mobile app? =

Yes!  You can customize your app in a number of ways:

1. Upload custom graphics for the load screen, logo, and other images for the app in the 'Logo, Images and Theme' tab in the Weever Apps Configuration screen
1. Copy templates/weever-content-single.php in the plugin folder to your current theme to customize the look of individual pages / posts, or rename to weever-content-single-{posttype}.php to override only for certain post types (standard or custom)
1. Add custom CSS in the 'Logo, Images and Theme' tab, under 'Advanced Theme Settings' or add a file named weever.css to your current Wordpress theme

You can determine the appropriate CSS classes to use by loading your private app URL in a Webkit browser such as Google Chrome or Safari, and inspecting the appropriate HTML elements.

Our support site - http://support.weeverapps.com/ - contains many more answers and our community forums.

== Screenshots ==

1. Select blog content by category, tag, search terms, and custom taxonomy
2. Sample page content
3. Easily add maps using standard Wordpress post geolocation data
4. Include social media feeds from Twitter, Identi.ca and Facebook
5. Event listing from Facebook or Google Calendar
6. Photos from Flickr, Facebook, Foursquare
7. Video streams from Youtube, Vimeo
8. Contact information
9. Forms generated using Wufoo
10. Add to launch screen ability with customizable icon

== Changelog ==

= 2.1.7 =

Added support for RSS feeds, changes to image uploading and saving

= 2.1 =

Initial version of weever appBuilder 2.0 plugin