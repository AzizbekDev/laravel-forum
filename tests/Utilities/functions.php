<?php
function create($class, $attribute = [])
{
    return factory($class)->create($attribute);
}

function make($class, $attribute = [])
{
    return factory($class)->make($attribute);
}