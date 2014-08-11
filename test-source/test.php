<?php

/***
 * @rest\title SwaggerGenTest API
 * @rest\description This is awesome
 * @rest\license MIT
 */

/**
 * @rest\api lists Operations about lists
 * @rest\produces application/json
 * @rest\apiversion 1.0.0
 * @rest\swaggerversion 1.2
 * @rest\errors 500 501
 */

/**
 * @rest\endpoint /lists This deals with lists and stuff
 */

/**
 * @rest\model List
 * @rest\property integer id unique
 * identifier for the list
 * @rest\property string name
 * @rest\property date date
 * @rest\property long user unique identifier
 * for the owner of this list
 */

/*
 * @rest\include paging.txt
 */

/**
 * @rest\method GET Get lists
 * @rest\form datetime start Start date/time
 * @rest\default 123
 * @rest\query string color
 * @rest\enum red green blue yellow
 * @rest\form? array(boolean) checkboxes List of booleans
 * @rest\error 401
 */

//@rest\errors 405 415