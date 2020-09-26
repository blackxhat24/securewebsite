<?php
/**
*   Luora website initialization script
*   COMP6183 - Secure Web Programming Quiz 2
*   LK17-1
*   
*/

/**
*   INITIALIZATION SCRIPT HTML TAGS
*   These are the essential tags to form the HTML
*   page of the initialization report
*/
?>

<html>
    <head>
        <title> Luora - Initialization Script </title>
        <?php include('components/include.php') ?>
    </head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
        <a class="navbar-brand" id="title" href="index.php" > Luora </a>
        </div>
    </div>
</nav>
    <div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <h1 class="page-header"> Luora Initialization Script </h1>
        </div>
    </div>

<?php
/**
*   DATABASE CONFIGURATION
*   This is the configuration for the mysql connection
*   The default setting should be the default configuration
*   of MySQL in Laboratory classes
*/
include('database_config.php');

/**
*   INITIALIZATION SCRIPT VARIABLES
*   This is the supporting variables required for this
*   initialization script to run
*/
include('helpers/debug.php');
function create_report($title, $content){
    $report_template = 
    '<div class="panel panel-default">
        <div class="panel-heading">{{title}}</div>
        <div class="panel-body">
            {{row}}
        </div>
    </div>';

    $row_template = 
    '<div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            {{body}}
        </div>
    </div>';

    $body_contents = [];
    if(is_array($content)){
        $body_contents = $content;
    }
    else{
        $body_contents[] = $content;
    }
    $report_string = str_replace("{{title}}", $title, $report_template);
    $row_string = '';
    foreach($body_contents as $body){
        $row_string .= str_replace("{{body}}", $body, $row_template);
    }
    $report_string = str_replace("{{row}}", $row_string, $report_string);
    return $report_string;
}

/**
*   PRE INTIALIZATION CHECK
*   Check whether the website has been initialized.
*   If the website has been initialized, a force command
*   must be given for the script to reinitialize everything.
*/ 
$preflight = new mysqli($server_address, $username, $password);
if($preflight->connect_error){
    $message = 'Pre-initialization connect failed: ' . $preflight->connect_error;
    die(create_report('Preflight Report', $message));
}

$db = "CREATE DATABASE IF NOT EXISTS $database";
$preflight->query($db);
$preflight->select_db($database);

$initialized = false;
$force = false;

$init_var_check = "SELECT `value` FROM app_config WHERE `key` = 'initialized'";
$check = $preflight->query($init_var_check);
if($check){
    $result = $check->fetch_assoc();
    if($result && $result['value'] == 'true'){
        $initialized = true;
    }
}

if($initialized){
    $force_param = isset($_REQUEST['force']);
    if($force_param){
        $preflight_report_string = create_report("Preflight Report", 'Data has been initialized. Force initialization is issued. This initialization script will re-initialize the application.');
        $force = true;
    }
    else{
        $preflight_report_string = create_report("Preflight Report", ['Data has been initialized. Please add force parameter if you want to re-initialize the application.', '<a href="?force" class="btn btn-warning">Force Initialization</a>']);
    }
}
else{
    $preflight_report_string = create_report("Preflight Report", 'Application is ready for initialization.');
}
echo($preflight_report_string);

/**
*   INITIALIZATION
*   Initialization will initializes everything required 
*   by the website
*   This step may only be run if the website has not been
*   initialized or it is forced to initialize
*/
if(!$initialized || $force){
    /**
    *   MYSQL CONNECTION
    *   This step initializes the MySQLi Instance for
    *   executing database queries.
    */
    $connection = new mysqli($server_address, $username, $password, $database);
    if($connection->connect_error){
        $message = 'Failed to connect to database: ' . $connection->connect_error;
        die(create_report('MySQLi Connect Report', $message));
    }
    $connection->set_charset('utf8');

    $message = "Successfully established database connection to $database@$server_address with username $username";
    echo(create_report('MySQLi Connect Report', $message));
    /**
    *   TABLE SCHEMA CREATION
    *   This step builds the tables required for the website to
    *   work properly.
    */
    $drop_user_table        = "DROP TABLE IF EXISTS users";
    $drop_article_table    = "DROP TABLE IF EXISTS articles";
    $drop_vote_table        = "DROP TABLE IF EXISTS votes";
    $drop_comment_table      = "DROP TABLE IF EXISTS comments";
    $drop_app_config_table  = "DROP TABLE IF EXISTS app_config";

    $user_table = 
    "CREATE TABLE users (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        username VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        PRIMARY KEY (id)
    )";

    $article_table = 
    "CREATE TABLE articles (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id INT UNSIGNED NOT NULL,
        title VARCHAR(255),
        content TEXT,
        tags VARCHAR(255),
        PRIMARY KEY (id)
    )";

    $vote_table = 
    "CREATE TABLE votes (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        article_id INT UNSIGNED  NOT NULL,
        user_id INT UNSIGNED  NOT NULL,
        PRIMARY KEY (id),
        INDEX (article_id, user_id)
    )";

    $comment_table =
    "CREATE TABLE comments (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        article_id INT UNSIGNED  NOT NULL,
        user_id INT UNSIGNED  NOT NULL,
        comment TEXT,
        PRIMARY KEY (id),
        INDEX (article_id, user_id)
    )";

    $app_config_table =
    "CREATE TABLE app_config (
        `key` VARCHAR(255),
        `value` VARCHAR(255),
        PRIMARY KEY (`key`)
    )";

    if( !$connection->query($drop_user_table) ||
        !$connection->query($drop_article_table) || 
        !$connection->query($drop_vote_table) ||
        !$connection->query($drop_comment_table) ||
        !$connection->query($drop_app_config_table)){
        $message = 'Drop tables failed: ' . $connection->error;
        die(create_report('Table Schema Creation Report', $message));
    }

    if( !$connection->query($user_table) ||
        !$connection->query($article_table) || 
        !$connection->query($vote_table) ||
        !$connection->query($comment_table) ||
        !$connection->query($app_config_table) ){
        $message = 'Table creation failed: ' . $connection->error;
        die(create_report('Table Schema Creation Report', $message));
    }

    echo(create_report('Table Schema Creation Report', ['Successfully drops all 5 tables (users, articles, votes, comments, app_config)', 'Successfully creates all 5 tables (users, articles, votes, comments, app_config)']));

    /**
    *   DATA SEEDER
    *   This step fills the database with dummy data that could
    *   be used to simulate a working website.
    */
    $insert_users = "INSERT INTO users (username, name, password, email) VALUES (?, ?, SHA1(?), ?)";
    $insert_users_stmt = $connection->prepare($insert_users);

    $insert_articles = "INSERT INTO articles (user_id, title, content, tags) VALUES (?, ?, ?, ?)";
    $insert_articles_stmt = $connection->prepare($insert_articles);

    $insert_votes = "INSERT INTO votes (article_id, user_id) VALUES (?, ?)";
    $insert_votes_stmt = $connection->prepare($insert_votes);

    $insert_comments = "INSERT INTO comments (article_id, user_id, comment) VALUES (?, ?, ?)";
    $insert_comments_stmt = $connection->prepare($insert_comments);

    $insert_app_config = "INSERT INTO app_config VALUES (?, ?)";
    $insert_app_config_stmt = $connection->prepare($insert_app_config);
    if( !$insert_users_stmt ||
        !$insert_articles_stmt ||
        !$insert_votes_stmt ||
        !$insert_comments_stmt ||
        !$insert_app_config_stmt ){
            $message = 'Statement preparation failed: ' . $connection->error;
        die(create_report('Statement Preparation Report', $message));
    }

    echo(create_report('Statement Preparation Report', 'Statement preparation successful'));

    //  Generate dummy user accounts
    $dummy_names = [
        "Lucy O'brien",
        "TJ HAFER",
        "Jonathon Dornbush",
        "Alex Gilyadov",
        "Terri Schwartz",
        "Mitchell Saltzman",
        "Miho Alf",
        "Alex Osborn",
        "John Ryan",
        "William Bibbilani",
        "Johnnie Tia",
        "Arvo Saburo",
        "Dorean Viljami",
        "Porter Mio",
        "Kaito Thekla"
    ];

    foreach($dummy_names as $name){
        //  Separate the names into first name and last name
        $names = explode(' ', $name);

        //  Username is lowercase of the first letter of first name 
        //  and last name
        $username = strtolower($names[0][0] . $names[1]);

        //  Email is lastname.firstname@luora.com
        $email = strtolower("$names[1].$names[0]@luora.com");

        //  Password is ... the same as username
        $password = $username;
        
        $insert_users_stmt->bind_param("ssss", $username, $name, $password, $email);
        $insert_users_stmt->execute();
    }

    //  Generate dummy articles
    $dummy_articles = [
        [
            'user_id' => 1,
            'title' => 'WOMEN IN VIDEO GAME DEVELOPMENT IN 2017: A SNAPSHOT',
            'content' => 'Several months ago, I asked 55 female and non-binary game development professionals from around the globe about the moment the light bulb switched on for them, the moment they thought video games are for me. Each answer was unique, "I would rent Diddy Kong Racing all the time from Blockbuster or Movie Gallery to the point that my dad had to buy it for me," said one. "I starting typing in programs from magazines on my dad\'s ZX81, then I begged my parents to buy my brother and I an Amstrad CPC 464 with a tape drive when I was 8," said another. Or, as one simply put it, "Fable 2."

Unique, yet familiar - these light bulbs are universal. They are genderless. So why do women currently constitute only 22 per cent of the video game industry?
In 2017, where the industry is perceptibly becoming more progressive, or at least, under more scrutiny than ever before, the gender split amongst gaming professionals needs to be probed. Not only with an eye to the question of why it exists, but with a more hopeful examination of what\'s being done to fix it? That message, above all, is worth spreading.

Here is what women working in video game development are saying about their industry. As is the case with many of us, these game developers\' light bulbs switched on early. A brightness felt while playing Mario Kart with siblings, or discovering the land of Hyrule. A dawning realisation that she too could create adventures for Kirby, scrawled on paper that would later become activity books for friends.

Trawling through these responses, I discovered another series of childhood constants: many were discouraged from their passion to get into video games at a young age, or had no idea that it was a career they could pursue at all.',
            'tags' => 'game;developer;industry'
        ],
        [
            'user_id' => 2,
            'title' => "BRIDGE CONSTRUCTOR PORTAL REVIEW",
            'content' => "As a puzzle game, Bridge Constructor Portal comes as a breath of fresh air. Its Lemmings-like rules are simple and intuitive, but mesh together to create a lot of interesting depth and challenge. Plus, getting to revisit Aperture Laboratories in some form for one of the few times since 2011's Portal 2 was a welcome treat.
The dubious science to be done in Bridge Constructor Portal involves safely guiding a vehicle with its gas pedal permanently floored across a set of clever obstacles to an exit point using metal girders and suspension wires to - as the name suggests - construct bridges. The challenging part is that the bridges, ramps, and towers you lay down can only attach to the level at set anchor points, and the realistic physics system requires that you distribute weight efficiently so that they're able to hold up under stress. 
The major twist separating it from previous Bridge Constructor games is, unsurprisingly, that many levels include paired sets of portals through which you can hurdle cars and other puzzle-related objects (such as the trusty companion cube), preserving their momentum. Especially in the later levels, they create some really crazy paths to victory that involve precisely tilted ramps and overlapping lanes of travel where you have to time everything just right if you don't want your test subjects colliding with each other in a giant fireball.
While some of the harder puzzles are nothing short of mind-boggling on first glance, Bridge Constructor Portal allows you an unlimited number of dry runs to try and get it right. This creates a satisfying progression, as you can focus on just getting the first part of the track sorted and then troubleshoot in steps. I always felt like I was making progress towards a solution, even when everything was going up in flames, and reaching the end of a level left me with a strong sense of satisfaction that was the culmination of many tweaks and \"A-ha!\" moments.",
            'tags' => 'portals;puzzle;games'
        ],
        [
            'user_id' => 3,
            'title' => 'STAR WARS: FIVE RIAN JOHNSON MOVIES, TV SHOWS TO WATCH AFTER THE LAST JEDI',
            'content' => 'Star Wars: The Last Jedi is undoubtedly Rian Johnson\'s biggest writing and directing gig yet, but Johnson made a name for himself with a host of unique and beloved films and TV episodes before traveling to a galaxy far, far away.
            
            Johnson started in much lower budget movie fare, but has created a few films heralded for their originality and ability to play with established form. He\'s also directed a few episodes of Breaking Bad and other shows, with some of his work being heralded as some of the best episodes of TV ever.
            
           While The Last Jedi has clearly proved divisive, if you enjoyed Johnson\'s work with Star Wars, be sure to check out IGN\'s picks in the gallery below for five movies and TV shows Johnson previously helmed.',
            'tags' => 'usa;tv;starwars;IGN'
        ],
        [
            'user_id' => 4,
            'title' => 'BLIZZARD JOB LISTING SUGGESTS NEW GAME WILL HAVE VEHICLES',
            'content' => 'A new Blizzard job listing suggests the company\'s unannounced project will incorporate vehicles.

The listing is for a Senior Software Engineer focusing on creating vehicles in "a robust first-person engine." The ideal candidate must have "proven experience in vehicle handling" and with "real-time rigid body physics and dynamic forces."

It\'s also important note the candidate needs to be comfortable with adding and working within an established code framework. This may suggest the technology for the unannounced title might already exist. 
Job listings for the game last year pointed to Blizzard looking for someone to work on a robust first-person engine for the unannounced project. Another, more recent listing from this year indicated the project may somehow be related to Overwatch.

For more on Overwatch, read about the game\'s recent update which adds an in-game notification for bans.',
            'tags' => 'blizzard;game;vehicles'
        ],
        [
            'user_id' => 5,
            'title' => 'HOW STAR WARS: EPISODE 9 SHOULD HANDLE CARRIE FISHER AND LEIA\'S ABSENCES',
            'content' => "One of the most bittersweet aspects of Star Wars: The Last Jedi is how much it sets up for General Leia Organa to do. The princess-turned-Resistance leader is a huge part of the movie but, according to everything we know, will not appear in the sequel.

Though Carrie Fisher was initially intended to have a bigger role in Episode IX than she did in Episode VIII, her death in 2016 means that Leia isn't slated to be in the final film in this new Star Wars trilogy. Lucasfilm boss Kathleen Kennedy even confirmed that the script for Episode IX was rewritten following Fisher's death so that Leia will not appear.
Though he's since been replaced by J.J. Abrams, original Episode IX director Colin Trevorrow said back in June that there are \"only certain things\" that he was \"willing to do\" with Leia in the new movie given Fisher's death. While Trevorrow did not disclose exactly how he'll deal with Fisher's passing, he did promise it'll be handled correctly. \"I can guarantee it will be handled with love and respect, and all of the soul that Carrie Fisher deserves,\" he said at the time.

It's unclear how that mission statement will change now that Abrams and Chris Terrio are rewriting the script that Trevorrow and Derek Connolly developed. We can hope that Kennedy stands by her assertion back in April 2017 that \"Carrie will not be in [Episode IX],\" a claim that includes not using of a digital recreation of the character like Lucasfilm did in Rogue One. But star Mark Hamill shared in October at New York Comic Con, which was after Abrams took over, that \"they're going to try and find a way to close [Leia's] story in IX that gives her the respect she deserves ... Certainly Leia was meant to be more prominent in IX.\"

Leia had a major role in The Last Jedi, ushering the remaining few Resistance fighters to a more hopeful future (and showing off her Force powers in the process). Her mission statement was to bring hope to the downtrodden across the galaxy, so it's easy to imagine the larger role she could have played in the new movie. Instead, The Last Jedi serves as an emotional sendoff to \"our Princess,\" as the credits sequence reads. And that should be the way to leave it.",
            'tags' => 'starwars;tv;stories'
        ],
        [
            'user_id' => 6,
            'title' => "DRAGON BALL FIGHTERZ - 8 CHARACTERS THAT NEED TO BE IN THE GAME",
            'content' => "With the recent announcements of Hit, Goku Black Rose, and Beerus, along with a release date of January 26 that's inching closer and closer, Dragon Ball FighterZ launch roster of playable characters is starting to look more and more set. However, it's still missing a number of characters that we feel need to make the cut.

With that said, here are 8 Dragon Ball characters that we still want to see in Dragon Ball FighterZ, and of course, be warned that spoilers for both Dragon Ball Z and Dragon Ball Super's all the way through to the most recent arc are ahead.
Broly is arguably the most iconic non-canonical Dragon Ball Z characters, to the point where it's almost unbelievable that he's a character that was only featured in movies considering his popularity. A Legendary Super Saiyan with muscles for days, Broly deserves to be in FighterZ for a number of reasons, but primarily because no one looks like him. The dude is huge! He could be the Potemkin, or Tager, or Abigail of the roster. A giant bruiser that deals huge damage at the expense of his speed, and yeah I know, Broly's not slow in the movies, but you know what, Yamcha loses every fight, and in this game, he could theoretically beat Majin Buu, so I think we'll be fine.

Look, if Gotenks can get a spot in Dragon Ball FigherZ, one would have to imagine that the single coolest fusion in all of Dragon Ball Z would get a spot too. Vegito is the fusion of Goku and Vegeta through the potara earings, and he's undisputably the most powerful character in Dragon Ball canon, pre Dragon Ball Super. I hear the complaint that we already have two versions of both Goku and Vegeta as playable characters, and Vegito might be a bit overkill, but I think there's enough unique about Vegito's move set and fighting style to make him work in FighterZ.

Androids 19 and 20 will be forever overshadowed by Androids 17 and 18, and perhaps rightfully so, but these two had a very interesting feature that makes them completely unique from the rest of the Dragon Ball Z cast -- They could absorb ki. That's a mechanic that would make them very intriguing in Dragon Ball FighterZ, a game that relies on a ki meter and where every character has a healthy amount of energy attacks. Android 19 would likely have to be the playable character here as Android 20 didn't really get to do much fighting, but he could serve as a support, much like how Android 17 currently supports 18. They might not be the coolest characters to include in FighterZ, but the duo could be one of the most mechanically interesting.

Jiren.
Another newcomer in Dragon Ball Super's most recent arc, Jiren is currently being built as the strongest opponent Goku has ever faced, with the possible exception of Beerus -- it's up for debate. In any case, Jiren is ridiculously strong, and having that kind of overwhelming strength represented in Dragon Ball FighterZ seems like a no brainer. It's easy to make a case for any of the members of the Pride Troopers to be characters in FighterZ, but if there's only room for just one, it obviously has to be Jiren.

Whis.
The angelic attendant of Beerus and the son of the Great Priest, Whis is a character who doesn't fight all that often, and when he does it's almost never seriously, but watching him train Goku and Vegeta is always a joy because of his calm and aloof personality. Whis is also one of the strongest characters in the series, but his strength feels much different than Goku, Jiren, or Beerus, and it would be very interesting to see how that is interpreted in fighting game form.",
            'tags' => 'dragonball;game;anime;action'
        ],
        [
            'user_id' => 4,
            'title' => 'DESTINY 2: BUNGIE CLARIFIES THREE OF COINS FUNCTIONALITY',
            'content' => "In a forum post, the developer explained it increases the chances a player will earn an Exotic engram by 50 percent after completing an activity. While players already knew Three of Coins increased their chances of earning exotic gear, they didn't know by how much, due to a vague item description.
            
            Bungie also said Heroic Strikes don't work with the item. As such, the studio is currently working on a fix it hopes to release sometime in early 2018. Later this week, the developer will discuss additional aspects of Destiny 2 it plans to address in the future.",
            'tags' => 'earning;game;developer'
        ],
        [
            'user_id' => 8,
            'title' => 'WOLFENSTEIN 2 SWITCH PORT HANDLED BY SAME STUDIO BEHIND DOOM, ROCKET LEAGUE PORTS',
            'content' => "Panic Button, the same development studio that ported Doom and Rocket League to Nintendo Switch, is also behind the upcoming Switch port of Wolfenstein 2: The New Colossus.

Machine Games' narrative designer Tommy Tordsson Bjork and senior game designer Andreas Ojerfors revealed Panic Button's involvement in an interview with Gamereactor. \"They're experts at the Switch and now they're experts with the [id Tech 6] engine so we work with them, and the Doom version turned out to be really kick-ass on the Switch so I think Wolfenstein will be the same,\" Ojerfors said. 
Ojerfors also spoke to Machine Games' interest in VR. While the team doesn't currently have plans to make a Wolfenstein 2 virtual reality experience, he said, \"we would love to do some VR experiments, absolutely, and we are very interested as a studio in creating that physical sense of being in the world, so it would be interesting to try that out in VR.\"

Wolfenstein 2 will be released for Switch sometime next year, and if comments from Bethesda's Pete Hines are any indication, this won't be the last game from the publisher to come to Nintendo's console. For more on Machine Games' hit first-person shooter, read IGN's Wolfenstein 2: The New Colossus review.

While you're at it, watch our Doom for Switch review below to find out why we were impressed by Panic Button's work on the port.",
            'tags' => 'games;designer;VR;Wolfenstein'
        ],
        [
            'user_id' => 9,
            'title' => 'WHY FAR CRY 5 COULD BE THE BEST FAR CRY YET',
            'content' => 'After nearly a decade of dropping players into exotic yet ferociously hostile locales like Indonesia, the Himalayas and most recently, prehistoric times - Far Cry 5 aims to bring players somewhere a little closer to home. Although Hope County is entirely fictitious, it\'s a gorgeous slice of Big Sky country, and the backdrop of the rocky mountains and luscious tree lines of the American Northwest makes it the perfect setting for the next Far Cry game.

After getting to explore a huge chunk of virtual Montana, Far Cry 5 feels like it has the potential to be the best version of what the franchise has had to offer since Far Cry 3 brought the series back into the mainstream in 2012. All of the signature elements that we\'ve come to expect are here. The gunplay is still incredibly satisfying, with firefights walking the tightrope between frantic tension and fast-paced fun as steadily as ever, and the sprawling forests, mountains and plains of Hope County, MT teem with secrets to explore, wildlife to hunt and bad guys to... well, also hunt.
You\'re able to add an additional layer of tactical depth (or explosive chaos) to these conquests by choosing one of several companions to help you out in a fight. We obviously chose Boomer, the adorably scruffy pup who\'s even more adorable when playing fetch with an assault rifle. The other Guns for Hire, like the sniper Grace or the bomb-happy pilot Nick, were useful in their own right, but there was something really special about having a trusty canine companion by your side while exploring the rural parts of America.

There is at least one series staple not returning, but it\'s a welcome change - at least to one of us. As the guy responsible for the lion\'s share of

It\'s this more down-to-earth feeling that\'s most striking about our time in Far Cry 5\'s single-player - a tick or two up on the "realism meter" that make the world feel a bit more lively and, well, real. It\'s little things, like those NPC interactions, or that enemy outposts are now functional locations in the community, like grain silos or fertilizer plants, instead of just a random lot with some buildings on it. Far Cry has always offered gorgeous locales and awesome dynamic gameplay - and that clearly hasn\'t changed - but the Rook Islands and Kyrat felt a little too surreal for us to fully immerse ourselves in. Maybe it\'s because, as Americans, we\'re more familiar with the setting, but Hope County seems like somewhere we could actually exist in.

Adding to that lived-in feeling is that fact that for the first time in the series, we\'re able to create our own characters. Although customization in the build we played was limited - we could choose to play as either a male or female deputy, though we\'re not sure if there will be more options beyond that at launch - it helped bolster the feeling that it was our story, not Jason Brody\'s or Ajay Ghales, especially thanks to the noticeable time spent on the details of the world and the characters in it.

We spent the majority of our time roaming about the open world, but the few missions we played did offer some of Far Cry\'s patented insanity. Helping a flamethrower-wielding madman torch a cattle farm, for example, or honoring a local stuntman by running the flaming obstacle course that killed him all feel thematically familiar and fun. The exploration of the world outside of these missions has always been the series\'s strong suit, though, and that\'s no different here - especially since you can always bring a friend.

As ludicrous as the situations we got ourselves into on our own may have been, they always paled in comparison to the shenanigans that ensued when we were playing together. For instance, returning from FC4 is the ability to grapple onto the rails of a helicopter flying above you, and latching on and having a buddy swing you wildly to and fro is stupidly entertaining - especially if they drop you over a convoy which you can then wingsuit down onto and launch off the road into a fiery mess (they actually hit JR with a truck and he died, but the cool explosion thing was the plan). The possibility for madcap hijinks is nearly endless given the litany of weapons and vehicles at your disposal, and being able to play through the entire story mode in co-op is a welcome addition as well.

Regardless of whether it\'s alone or with friends - though let\'s be honest, who wouldn\'t bring a pal to this party - we\'re excited to walk back into the wooded hills of Far Cry 5, and to see everything else that Hope County has to offer.',
            'tags' => 'farcry;games'
        ],
        [
            'user_id' => 10,
            'title' => 'THE BEST REVIEWED MOVIES OF 2017',
            'content' => "The year is coming to a close, and the time has come to look back at all the films that blew our minds. IGN's film critics watched a heck of a lot of movies this year, and these were the very best.

            IGN rates its movies on a scale of 0-10, and any film released between January 1 and December 31st is eligible for this slideshow, which highlights all the films that earned an 8 or above from any of our various film critics.

On this scale, any film rated 8.0-8.9 is considered \"great\", any film rated 9.0-9.9 is considered \"amazing\" and anything that gets the coveted 10 rating is declared a \"masterpiece\". And we don't give 10s out to just anything, either. Only one motion picture earned a 10/10 rating in all of 2017. (The lowest score of the year: 1.9, yikes.).

Many of the most successful movies of the year didn't even rank above an 8, so you won't see them here. (Sorry, Wonder Woman and Thor: Ragnarok.) Also, many of the films that did earn our highest accolades were relatively small releases, so if you missed them, now you know you need to seek them out immediately. You'll be glad you did.

So scroll through the slideshow for the films our critics singled out as the best of 2017, click the links to their reviews to find out more, and tell us what your favorite films of the year were!",
            'tags' => 'movies;rating;reviews'
        ],
    ];

    foreach($dummy_articles as $article){
        $user_id = $article['user_id'];
        $title = $article['title'];
        $content = $article['content'];
        $tags = $article['tags'];
        $insert_articles_stmt->bind_param("isss", $user_id, $title, $content, $tags);
        $insert_articles_stmt->execute();
    }

    //  Create dummy comment
    $dummy_comments = [
        [
            'user_id' => 10,
            'article_id' => 1,
            'comment' => "Wow nice, i'll wait for it!"
        ],
        [
            'user_id' => 5,
            'article_id' => 2,
            'comment' => "Really ? "
        ],
        [
            'user_id' => 5,
            'article_id' => 3,
            'comment' => "Well, Overall it's Awesome !!"
        ],
        [
            'user_id' => 7,
            'article_id' => 3,
            'comment' => "If it's real, i will vote 4 it :DD..."
        ],
        [
            'user_id' => 1,
            'article_id' => 4,
            'comment' => "Don't see it coming..."
        ],
        [
            'user_id' => 10,
            'article_id' => 5,
            'comment' => "I don't think so...."
        ],
        [
            'user_id' => 9,
            'article_id' => 5,
            'comment' => "Hmm.. You should consider it first before publishing this article..."
        ],
        [
            'user_id' => 8,
            'article_id' => 6,
            'comment' => "Ohh yeah, dragon ball... xDD"
        ],
        [
            'user_id' => 3,
            'article_id' => 6,
            'comment' => "DRAGON BALL, my favorite anime ^_^..."
        ]
    ];
    foreach($dummy_comments as $comment){
        $article_id = $comment['article_id'];
        $user_id = $comment['user_id'];
        $comment_content = $comment['comment'];
        $insert_comments_stmt->bind_param("iis", $article_id, $user_id, $comment_content);
        $insert_comments_stmt->execute();
    }


    //  Insert initialization key to prevent this script from re-initializing
    //  the website
    $key = 'initialized';
    $value = 'true';
    $insert_app_config_stmt->bind_param("ss", $key, $value);
    $insert_app_config_stmt->execute();

    echo(create_report('Table Seeder Report', 
        ['User data entered ('. count($dummy_names) . ' data)',
        'Article data entered (' . count($dummy_articles) .' data)',
        'Comment data entered (' . count($dummy_comments) . ' data)']
    ));

    $name = $dummy_names[rand(0, count($dummy_names) - 1)];
    $names = explode(' ', $name);
    $username = strtolower($names[0][0] . $names[1]);
    echo(create_report('Initialization Completed', [
        "You can log in with this credential (username/password): $username/$username",
        "All default credentials generated through this initialization has the password set equal to the username",
        '<a href="index.php" class="btn btn-link">Open Website</a>'
    ]));
}
?>
    </div>
</body>
</html>