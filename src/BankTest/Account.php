<?php declare(strict_types=1);

namespace BankTest;

use BankTest\Bank;
use InvalidArgumentException;   // Should be a custom ValidationException, since it's not necessarily a type mismatch.
use UnexpectedValueException;

/**
 * Account class
 *
 * A representation of a a single account within a bank.
 *
 * @package BankTest
 */
class Account
{
    /**
     * Account "number" - unique alphanumeric string.
     * @var string
     */
    private $accountNumber;

    /**
     * Current account balance - in cent/øre to avoid rounding errors.
     * @var int
     */
    private $balance;

    /**
     * List of deposits to this account.
     * @var array
     */
    private $deposits = [];

    /**
     * List of withdrawals from this account.
     * @var array
     */
    private $withdrawals = [];


    /**
     * Set/change account's number
     *
     * @param  string  $account_number  The account number
     * @param  \BankTest\Bank  $bank     The parent bank, needed for validation
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @return void
     */
    public function setAccountNumber(string $account_number, Bank $bank): void
    {
        if (! preg_match('/^[a-z0-9]+$/i', $account_number)) {
            throw new InvalidArgumentException('Account number must be alphanumeric and not empty');
        }

        // Here I need to ensure the account number is unique, which requires access to the bank.
        // Seeing as I don't want to create a tight coupling, I'm going to need the bank as an argument.
        // Could probably be fixed nicer, perhaps with some kind of BankHandler superclass in which
        // both objects could be injected.
        // Depending on how account numbers work, I could even need a list of all accounts in all banks.
        $existing_account_numbers = $bank->getAccountNumbers();
        if (in_array($account_number, $existing_account_numbers)) {
            throw new UnexpectedValueException('Account number already in use');
        }

        $this->accountNumber = $account_number;
    }

    /**
     * Add deposit in the specified amount
     *
     * @param  int  $deposit  Amount in cent/øre
     * @return void
     */
    public function addDeposit(int $deposit): void
    {
        $this->deposits[] = $deposit;
    }

    /**
     * Add withdrawal in the specified amount
     *
     * @param  int  $withdrawal  Amount in cent/øre
     * @return void
     */
    public function addWithdrawal(int $withdrawal): void
    {
        $this->withdrawals[] = $withdrawal;
    }

    /**
     * Set balance from deposits and withdrawals
     *
     * Note: I didn't like the prefix set- when it took no args
     * Also, I'm assuming this is supposed to reset the balance to zero first.
     *
     * @return void
     */
    public function calculateBalance(): void
    {
        $this->balance = 0;

        foreach ($this->withdrawals as $withdrawal) {
            $this->balance = $this->balance - $withdrawal;
        }

        foreach ($this->deposits as $deposit) {
            $this->balance = $this->balance + $deposit;
        }
    }

    /**
     * Accessors
     */

    /**
     * Get current balance
     *
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * Get account number
     *
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * Get deposits
     *
     * @return array
     */
    public function getDeposits(): array
    {
        return $this->deposits;
    }

    /**
     * Get withdrawals
     *
     * @return array
     */
    public function getWithdrawals(): array
    {
        return $this->withdrawals;
    }
}
