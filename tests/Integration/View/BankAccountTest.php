<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\BankAccount;
use Enlivy\Organization\BankTransaction;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for BankAccount-related endpoints.
 */
class BankAccountTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Bank Accounts
    // -------------------------------------------------------------------------

    public function testListBankAccounts(): void
    {
        $bankAccounts = $this->getClient()->bankAccounts->list();

        $this->assertInstanceOf(Collection::class, $bankAccounts);
        $this->assertIsArray($bankAccounts->data);

        if (count($bankAccounts->data) > 0) {
            $bankAccount = $bankAccounts->data[0];
            $this->assertInstanceOf(BankAccount::class, $bankAccount);
            $this->assertNotNull($bankAccount->id);
            $this->assertNotNull($bankAccount->organization_id);
        }
    }

    public function testListBankAccountsWithPagination(): void
    {
        $bankAccounts = $this->getClient()->bankAccounts->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $bankAccounts);
        $this->assertNotNull($bankAccounts->meta);
    }

    public function testRetrieveBankAccount(): void
    {
        $bankAccounts = $this->getClient()->bankAccounts->list(['per_page' => 1]);

        if (count($bankAccounts->data) === 0) {
            $this->markTestSkipped('No bank accounts available for testing');
        }

        $bankAccountId = $bankAccounts->data[0]->id;
        $bankAccount = $this->getClient()->bankAccounts->retrieve($bankAccountId);

        $this->assertInstanceOf(BankAccount::class, $bankAccount);
        $this->assertEquals($bankAccountId, $bankAccount->id);
    }

    // -------------------------------------------------------------------------
    // Bank Transactions
    // -------------------------------------------------------------------------

    public function testListBankTransactions(): void
    {
        $transactions = $this->getClient()->bankTransactions->list();

        $this->assertInstanceOf(Collection::class, $transactions);
        $this->assertIsArray($transactions->data);

        if (count($transactions->data) > 0) {
            $transaction = $transactions->data[0];
            $this->assertInstanceOf(BankTransaction::class, $transaction);
        }
    }

    public function testRetrieveBankTransaction(): void
    {
        $transactions = $this->getClient()->bankTransactions->list(['per_page' => 1]);

        if (count($transactions->data) === 0) {
            $this->markTestSkipped('No bank transactions available for testing');
        }

        $transactionId = $transactions->data[0]->id;
        $transaction = $this->getClient()->bankTransactions->retrieve($transactionId);

        $this->assertInstanceOf(BankTransaction::class, $transaction);
        $this->assertEquals($transactionId, $transaction->id);
    }
}
