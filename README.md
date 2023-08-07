# GenRanks
TALL based website to make a C&C General's Zero-Hour leaderboard.

**TailwindCSS, AlpineJS, Laravel, Livewire**

## The goal
* **Visually Epic** The main vision for the new site is that it must be visually epic and aesthetically pleasing. This means that upon landing on the site; the branding, the colours, the layout and information has a real wow-factor for anyone who lands there. Take AOE4 website as a base but improve on that. The user should be instantly and clearly be able to see who the top competetive players are currently. The landing page must also show clearly somewhere the currently monthly prize and number of unique players for that month under a header like "activity".
* **All GenTool Users** The site must (somewhere) contain the statistics for all players that use GenTool. The reason for this is to show the world how active Zero Hour is. There are sometimes approx. 10,000 unique players on GenTool each month but only around 100 competetive players who take part on Clanwars.cc. We want to show the world the 10,000 number. The best way to do this would be to have a Competive Ladder and Casual Ladder. All players automatically take part in Casual Ladder but you enter Competetive Ladder by ticking a box somewhere, perhaps on the new website profile. How we achieve this is down to the developers and linked to the next point.
* **Effortless Setup** Installing a DLL file through an installer might initially be ok to get the project off the ground and tested but the long term goal should be virtually no work needed from the user. Someone who has the latest GenTool should already be shown on the ladder, even without accessing the website or installing anything.
* **A World-Class ELO System** It is imperative that as a base, we take an already established organistation and copy their ELO system, only tweaking as we see neccessary for our game. We do not need to re-create the wheel. We must take an already well-respected system and initially copy it, only with minor tweaks here and there.
* **Shown on Google** A combination of a great website name, with strong SEO should bring in at least some clicks from a Google search. Anyone typing in "cnc" "command and conquer" "cnc ranks" "cnc ladder" "zh ladder" "zh ranks" "how to install zh" should be able to find our new site at the top of a Google search.

## Feedback
Join our Discord or make an issue on Github, if you want to give feedback, have cool ideas or just want to talk.

## Programming
This is a quick start guide!

### Software
* PHP
* Composer
* Yarn / NPM
* A database
* GIT

### Databases
MariaDB 10.3+, MySQL 5.7+, PostgreSQL 10.0+, SQLite 3.8.8+, SQL Server 2017+

### Setup
After doing the following steps, you should be setup and ready to develop locally.
1. Download the repo locally.
2. Copy the `.env.example` into `.env`
3. Fill in the `.env` file so that it fits your local development environment.
4. Get node packages with either `yarn install` or the NPM equivalent.
5. Run composer with `composer install` to set up all PHP packages
6. Make sure to link the storage to public using `php artisan storage:link`.
7. Make sure your database is running.
8. Run database migrations with `php artisan migrate`.
9. Start your website `php artisan serve`, and go to the URL shown.

### Socialite
To obtain the required secrets, keys, and IDs for the mentioned social media platforms, you'll need to create developer applications or register your application with each respective platform. Here's a guide on how to obtain these details for each platform:

* **YouTube**:
    Visit the Google Developers Console [Google](https://console.developers.google.com/).
    Create a new project or select an existing project.
    Enable the YouTube Data API for the project.
    Go to the "Credentials" section and create new OAuth 2.0 credentials.
    Obtain the Client ID, Client Secret, and specify the Redirect URI.

* **Twitter**:
    Visit the Twitter Developer Portal [Twitter](https://developer.twitter.com/).
    Create a new application.
    Obtain the API Key (Client ID) and API Secret Key (Client Secret).
    Set the Callback URL (Redirect URI).

* **Reddit**:
    Visit the Reddit Developer Portal [Reddit](https://www.reddit.com/prefs/apps).
    Create a new application.
    Obtain the Client ID and Client Secret.
    Set the Redirect URI.

* **Discord**:
    Visit the Discord Developer Portal [Discord](https://discord.com/developers/applications).
    Create a new application.
    Obtain the Client ID and Client Secret.
    Set the Redirect URI and any optional settings.

* **Steam**:
    Visit the Steam Developer Portal [Steam](https://steamcommunity.com/dev/apikey).
    Obtain the Steam API Key by following the instructions on the page.
    Specify the Redirect URI and allowed hosts in your application.

* **Twitch**:
    Visit the Twitch Developer Console [Twitch](https://dev.twitch.tv/console/apps).
    Create a new application.
    Obtain the Client ID and Client Secret.
    Set the Redirect URI.

* **Facebook**:
    Visit the Facebook Developer Portal [Facebook](https://developers.facebook.com/).
    Create a new application.
    Obtain the App ID (Client ID) and App Secret (Client Secret).
    Set the Redirect URI.

Once you have obtained the required secrets, keys, and IDs for each social media platform, you can add them to your `.env` file.

---

**Note:** GenRanks is not affiliated with or endorsed by Electronic Arts Inc. or any of its subsidiaries.
