<?php declare(strict_types=1);

namespace BankTest;

use BankTest\Account;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Bank class
 *
 * A representation of a an entire bank.
 *
 * @package BankTest
 */
class Bank
{
    /**
     * Name of the bank
     * @var string
     */
    private $bankName;

    /**
     * Address of bank
     * @var string
     */
    private $address;

    /**
     * Array of bank accounts
     * @var array
     */
    private $bankAccounts = [];


    /**
     * Change name of the bank
     *
     * @param  string  $name  The new name of the bank
     * @throws InvalidArgumentException
     * @return void
     */
    public function setBankName(string $name): void
    {
        // Ensure name isn't empty. Should probably have more validation than that, but...
        if (empty($name)) {
            throw new InvalidArgumentException('Name cannot be empty');
        }

        $this->bankName = $name;
    }

    /**
     * Set bank accounts
     *
     * I'm honestly not entirely sure what this is supposed to do.
     * Overwrite all accounts? Sounds dangerous.
     *
     * @param  array  $bank_accounts  I don't know
     * @return void
     */
    public function setBankAccounts(array $bank_accounts): void
    {
        $this->bankAccounts = $bank_accounts;
    }

    /**
     * Set bank physical address
     *
     * @param  string  $address  New physical address
     * @throws InvalidArgumentException
     * @return void
     */
    public function setAddress(string $address): void
    {
        if (empty($address)) {
            throw new InvalidArgumentException('Address cannot be empty');
        }

        $this->address = $address;
    }

    /**
     * Add a new Account object to the internal list
     *
     * @param  \BankTest\Account  $bank_account  The new account object
     * @throws InvalidArgumentException
     * @return void
     */
    public function addBankAccount(Account $bank_account): void
    {
        // Add extra check for pre-existing account numbers, as we can't
        // expect addBankAccount to always be run immediately after setAccountNumber.
        foreach ($this->bankAccounts as $existing_account) {
            if ($bank_account->getAccountNumber() === $existing_account->getAccountNumber()) {
                throw new UnexpectedValueException('Account number already in use');
            }
        }

        $this->bankAccounts[] = $bank_account;
    }

    /**
     * Get bank name and address as a concatenated string
     *
     * @return string
     */
    public function getPostalAddressForPrintLabels(): string
    {
        return $this->bankName . "\n" . $this->address;
    }

    /**
     * Get all account numbers in the current bank.
     *
     * @return array
     */
    public function getAccountNumbers(): array
    {
        $output_numbers = [];

        foreach ($this->bankAccounts as $bank_account) {
            $output_numbers[] = $bank_account->getAccountNumber();
        }

        return $output_numbers;
    }

    /**
     * Count number of accounts in this bank
     *
     * @return int
     */
    public function countAccounts(): int
    {
        return count($this->bankAccounts);
    }

    /**
     * Transfer money between two Account objects
     *
     * @param  \BankTest\Account  $source_account  Account to transfer from
     * @param  \BankTest\Account  $target_account  Account to transfer to
     * @param  int  $amount  Amount to transfer. In cent/øre to avoid float rounding errors.
     * @throws InvalidArgumentException
     * @return void
     */
    public function doInternalTransaction(Account $source_account, Account $target_account, int $amount): void
    {
        $source_account_is_internal = false;
        $target_account_is_internal = false;

        // Only allow transactions between accounts in this bank
        foreach ($this->bankAccounts as $bank_account) {
            if ($bank_account->getAccountNumber() === $source_account->getAccountNumber()) {
                $source_account_is_internal = true;
            }

            if ($bank_account->getAccountNumber() === $target_account->getAccountNumber()) {
                $target_account_is_internal = true;
            }
        }

        // No need for two loops doing the same.
        // ... Unless we add a `break` after the data is found, in which case it would
        // depend if the found data is more than halfway through the array. Meh.
        
        // foreach ($this->bankAccounts as $bank_account) {
        //     if ($bank_account->accountNumber === $target_account->accountNumber) {
        //         $target_account_is_internal = true;
        //     }
        // }

        if (! $source_account_is_internal) {
            throw new InvalidArgumentException('Source account is not internal');
        }

        if (! $target_account_is_internal) {
            throw new InvalidArgumentException('Target account is not internal');
        }

        // This would of course need to be much more advanced, to ensure nothing goes wrong
        // in the first bit before running the second, and rolling the first back in case
        // the second dies. But I'm guessing that's beyond scope of this task.
        $source_account->addWithdrawal($amount);
        $target_account->addDeposit($amount);
    }
}
