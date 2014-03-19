<?php

function bail_out($code, $message) {
	exit(json_encode(array('error' => $code, 'message' => $message)));
}