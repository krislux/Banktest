<?php
/**
 * This file contains two classes which represents a bank and its bank accounts.
 * Both classes are very badly implemented following no standards and contains a lot of errors.
 *
 * The Bank class represents a bank
 * The bank have these main features:
 *  - Has zero or more bank accounts
 *  - Must have an address.
 *  - Must have a name
 *  - Can perform a money transfer between accounts in the bank.
 *  - Can return a "Postal Address" created from the name of the bank and the address.
 *
 * The Account class represents a bank account.
 * A bank account has these main features:
 *  - Must have a unique account_number
 *  - Knows its current balance
 *  - Knows its history of transactions (withdrawels and deposits)
 *
 * After the two class implementations three crude tests has been written,
 * which currently all succeed with the current implementation
 *
 * Please refactor both classes and achieve the following:
 *  - Refactored classes should follow PSR-2 coding style guide
 *  - They should be robust and testable
 *  - All errors should be eliminated
 *  - The code should be sufficiently documented
 *
 * You are allowed to do any kind of refactor including renaming classes, function, attributes or
 * introducing breaking changes etc, as long as the three tests continue to work.
 * If needed you are allowed to update the tests as well, if they no longer comply with your refactored code.
 *
 *
 *
 * Have fun :)
 */


class Bank
{
    public $bankName;
    public $bank_Accounts;

    function setBankName($name){
        $this->bankName = $name;
    }

    function setBankAccounts($bAs){
        $this->bank_Accounts = $bAs;
    }

    function setAddress($adress){
        $this->address = $adress;
    }

    function addBankAccount($ba){
        $this->bank_Accounts[] = $ba;
    }

    function getPostalAddressForPrintLabels(){
        return $this->bankName ."\n".$this->address;
    }

    function doInternalTransaction($one_acc, $another_acc, $amount){

        // Only allow transactions between accounts in this bank
        foreach ($this->bank_Accounts as $bank_Account){
            if($bank_Account->account_number == $one_acc->account_number){
                $one_acc_is_internal = true;
            }
        }

        foreach ($this->bank_Accounts as $bank_Account){
            if($bank_Account->account_number == $another_acc->account_number){
                $another_acc_is_internal = true;
            }
        }

        if($one_acc_is_internal){
            if($another_acc_is_internal){
                $one_acc->AddWithdrawels($amount);
                $another_acc->AddDeposit($amount);
            }
        }
    }

}

class account {

    public $account_number;
    public $_balance;
    public $_deposits;
    public $_withdrawals;

    function set_account_number($account_number){
        $this->account_number = $account_number;
    }

    function AddDeposit($deposit){
        $this->_deposits[] = $deposit;

    }

    function AddWithdrawels($withDrawal){
        $this->_withdrawals[] = $withDrawal;
    }

    /**
     * Get Balance
     * @return int
     */
    function getBalance(){
        return $this->_balance;
    }

    /**
     * Set Balance from deposits and withdrawals
     */
    function setBalance(){
        foreach($this->_withdrawals as $withdrawal){
            $this->_balance = $this->_balance - $withdrawal;
        }

        foreach($this->_deposits as $deposit){
            $this->_deposits = $this->_deposits + $deposit;
        }
    }




}

/**
 * The following is three crude tests written to test the code
 */

/*
 * Test 1:
 * Initialize a bank test generation of the postal address method
 *
 */
$bank = new Bank();
$bank_name = 'SOME BANK';
$bank->setBankName($bank_name);
$bank_address = 'Some Street,\\nCopenhagen';
$bank->setAddress($bank_address);


$postal_address = $bank->getPostalAddressForPrintLabels();
$expected_postal_address = $bank_name . "\n" . $bank_address;
if($expected_postal_address !== $postal_address){
    echo "Failed to get Postal address\n";
    exit();
}

/*
 * Test 2:
 * Create two bank accounts and add them to a bank
 */
$first_account_number = 'ab01';
$first_account = new Account();
$first_account->set_account_number($first_account_number);

$second_account_number = 'qj42';
$second_account = new Account();
$second_account->set_account_number($second_account_number);

$bank->addBankAccount($first_account);
$bank->addBankAccount($second_account);
$number_of_accounts = count($bank->bank_Accounts);
$expected_number_of_accounts = 2;

if($expected_number_of_accounts !== $number_of_accounts){
    echo "Failed to assign accounts\n";
    exit();
}

/*
 * Test 3:
 * Transfer 100 DKK from the first account to the second
 */
$bank->doInternalTransaction($first_account, $second_account, 100);

$number_of_withdrawels_in_first_account = count($first_account->_withdrawals);
$expected_number_of_withdrawels_in_first_account = 1;
if($expected_number_of_withdrawels_in_first_account !== $number_of_withdrawels_in_first_account){
    echo "Failed to withdraw from first account\n";
    exit();
}

$number_of_deposits_in_second_account = count($second_account->_deposits);
$expected_number_of_deposits_in_second_account = 1;
if($expected_number_of_deposits_in_second_account !== $number_of_deposits_in_second_account){
    echo "Failed to deposit to second account\n";
    exit();
}


echo "All seems fine !\n";