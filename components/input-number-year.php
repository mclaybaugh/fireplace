<?php

function fireplace_input_number_year($name, $isRequired = true)
{
    fireplace_input_number($name, '1900', '2100', '1', $isRequired);
}