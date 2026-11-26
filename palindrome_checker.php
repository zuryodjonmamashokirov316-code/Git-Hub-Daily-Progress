<?php
function isPalindrome($word) {
    $word = strtolower($word);
    return $word === strrev($word);
}

$word = "level";

if (isPalindrome($word)) {
    echo "$word is a palindrome";
} else {
    echo "$word is not a palindrome";
}
?>
