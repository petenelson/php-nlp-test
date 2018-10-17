<?php

require_once 'vendor/autoload.php';
require_once 'singularize.php';

use NlpTools\Tokenizers\WhitespaceTokenizer;
use NlpTools\Similarity\CosineSimilarity;

if ( count( $argv ) <= 1 ) {
	echo 'Input filename is required.' . PHP_EOL;
	return;
}

$filename = $argv[1];

if ( ! file_exists( $filename ) ) {
	echo "Input filename {$filename} does not exist." . PHP_EOL;
	return;
}

$content = file_get_contents( $filename );

if ( ! empty( $content ) ) {
	$content = strip_tags( $content );
}

if ( empty( $content ) ) {
	echo "Input filename {$content} is empty." . PHP_EOL;
	return;
}

$stop_words = explode( PHP_EOL, file_get_contents( 'stop-words.txt' ) );
$strip_chars = explode( ' ', '. ! , $ \' " ? ; : @ # % & * ( ) - + = ’ [ ] —');

$tokenizer = new WhitespaceTokenizer();
$sim = new CosineSimilarity();
 
$results = $tokenizer->tokenize( $content );
 
$tokens = [];

if ( is_array( $results ) ) {

	foreach ( $results as $token ) {

		$token = str_replace( $strip_chars, '', strtolower( $token ) );

		$token = singularize( $token );

		if ( strlen( $token ) >= 3 && ! in_array( $token, $stop_words ) ) {

			if ( ! isset( $tokens[ $token ] ) ) {
				$tokens[ $token ] = 1;
			} else {
				$tokens[ $token ]++;
			}
		}
	}
}

arsort( $tokens );

$tokens = array_slice( $tokens, 0, 30, true );

print_r( $tokens );
