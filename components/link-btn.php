<?php

function fireplace_link_btn($href, $text, $class = '', $target = '_self')
{
    fireplace_link($href, $text, "btn $class", $target);
}