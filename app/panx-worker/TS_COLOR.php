<?php
/*
TEXT:
***********

black
dark_gray
blue
light_blue
green
light_green
cyan
light_cyan
red
light_red
purple
light_purple
brown
yellow
light_gray
white

BACKGROUND:
***********
black
red
green
yellow
blue
magenta
cyan
light_gray
*/
function colorize($TEXT, $TEXT_COLOR = null, $BACKGROUND_COLOR = null) {
    $TEXT_COLORS = array('black' => '0;30', 'dark_gray' => '1;30', 'blue' => '0;34', 'light_blue' => '1;34', 'green' => '0;32', 'light_green' => '1;32', 'cyan' => '0;36', 'light_cyan' => '1;36', 'red' => '0;31', 'light_red' => '1;31', 'purple' => '0;35', 'light_purple' => '1;35', 'brown' => '0;33', 'yellow' => '1;33', 'light_gray' => '0;37', 'white' => '1;37');
    $BACKGROUND_COLORS = array('black' => '40', 'red' => '41', 'green' => '42', 'yellow' => '43', 'blue' => '44', 'magenta' => '45', 'cyan' => '46', 'light_gray' => '47');
    return "\e[".$TEXT_COLORS[$TEXT_COLOR].";".$BACKGROUND_COLORS[$BACKGROUND_COLOR]."m".$TEXT."\e[0m";

}