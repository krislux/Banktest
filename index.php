<?php
/**
 * =====================================
 * See original.php for test description
 * =====================================
 */

require 'vendor/autoload.php';

/**
 * Note: Obviously tests don't belong in the index file either, but for the sake
 * of simplicity I'm culling it at three files, since having tests here doesn't break
 * PSR-2 compliance, whereas classes not having individual files does.
 */

use BankTest\Bank;
use BankTest\Account;

/**
 * The following is three crude tests written to test the code
 */

/*
 * Test 1:
 * Initialize a bank test generation of the postal address method
 */
$bank = new Bank();
$bank_name = 'SOME BANK';
$bank->setBankName($bank_name);
$bank_address = "Some Street,\nCopenhagen";
$bank->setAddress($bank_address);


$postal_address = $bank->getPostalAddressForPrintLabels();
$expected_postal_address = $bank_name . "\n" . $bank_address;
if ($expected_postal_address !== $postal_address) {
    echo "Failed to get Postal address\n";
    exit();
}

/*
 * Test 2:
 * Create two bank accounts and add them to a bank
 */
$first_account_number = 'ab01';
$first_account = new Account();
$first_account->setAccountNumber($first_account_number, $bank);

$second_account_number = 'qj42';
$second_account = new Account();
$second_account->setAccountNumber($second_account_number, $bank);

$bank->addBankAccount($first_account);
$bank->addBankAccount($second_account);
$number_of_accounts = $bank->countAccounts();
$expected_number_of_accounts = 2;

if ($expected_number_of_accounts !== $number_of_accounts) {
    echo "Failed to assign accounts\n";
    exit();
}

/*
 * Test 3:
 * Transfer 100 DKK from the first account to the second
 */
$bank->doInternalTransaction($first_account, $second_account, 100);

$number_of_withdrawals_in_first_account = count($first_account->getWithdrawals());
$expected_number_of_withdrawals_in_first_account = 1;
if ($expected_number_of_withdrawals_in_first_account !== $number_of_withdrawals_in_first_account) {
    echo "Failed to withdraw from first account\n";
    exit();
}

$number_of_deposits_in_second_account = count($second_account->getDeposits());
$expected_number_of_deposits_in_second_account = 1;
if ($expected_number_of_deposits_in_second_account !== $number_of_deposits_in_second_account) {
    echo "Failed to deposit to second account\n";
    exit();
}

echo "All seems fine !\n";
