<?php
// hash_test.php

// Ganti ini dengan hash yang ingin Anda cek.
$hash = '$2y$10$v1z2mNfWpW.qx1n5Axlz5OKePBNQJErmIMDpqA3Nn7dA1zSkjKQmS';

// Coba password apa yang ingin Anda tes. Misalnya 'admin123'.
$passwordToTest = 'admin123';

// Tes apakah $passwordToTest cocok dengan $hash
$isMatch = password_verify($passwordToTest, $hash);

// Tampilkan hasil
if ($isMatch) {
    echo 'Hash cocok dengan password: ' . $passwordToTest;
} else {
    echo 'Hash TIDAK cocok dengan password: ' . $passwordToTest;
}
?>
