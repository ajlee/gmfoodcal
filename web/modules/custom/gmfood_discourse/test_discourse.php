<?php

namespace richp10\discourseAPI;
require_once "vendor/discourse_api/src/DiscourseAPI.php";


echo getenv('DISCOURSE_KEY');

$api = new \richp10\discourseAPI\DiscourseAPI("forum.gmfoodforum.org", getenv('DISCOURSE_KEY'), 'https');

echo "create user";
// create user
$r = $api->createUser('John Doe', 'johndoe', 'alexjameslee@gmail.com', 'foobar!!');
print_r($r);

echo "create cat";
$r = $api->createCategory('a new category', 'cc2222');
print_r($r);

$category = $api->getCategory('development');

print_r($category);

// create a topic
echo "create topic";
$r = $api->createTopic(
    'This is the title of a brand another time topic',
    "This is the body text of a brand new topic. I really don't know what to say",
    26,
    "drupal_admin"
);
print_r($r);

echo "get development category";
$category = $api->getCategories();
print_r($category);

print_r(get_class_methods($api));
