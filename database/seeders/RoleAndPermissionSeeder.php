<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Melbedran\RolePermession\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesData = [
            'Branch Manager' => [
                'description' => 'Branch Manager with authority over branch operations, loan approvals, vault, accounts, and reports.',
                'abilities' => [
                    'branches.view' => 'allow',
                    'branches.update' => 'allow',
                    'customers.view' => 'allow',
                    'customers.create' => 'allow',
                    'customers.update' => 'allow',
                    'customers.kyc_review' => 'allow',
                    'accounts.view' => 'allow',
                    'accounts.create' => 'allow',
                    'accounts.freeze' => 'allow',
                    'accounts.close' => 'allow',
                    'transactions.view' => 'allow',
                    'transactions.create' => 'allow',
                    'transactions.reverse' => 'allow',
                    'transfers.view' => 'allow',
                    'transfers.create' => 'allow',
                    'transfers.cancel' => 'allow',
                    'transfers.retry' => 'allow',
                    'cards.view' => 'allow',
                    'cards.create' => 'allow',
                    'cards.activate' => 'allow',
                    'cards.block' => 'allow',
                    'cards.pin_manage' => 'allow',
                    'cards.limits' => 'allow',
                    'loans.view' => 'allow',
                    'loans.create' => 'allow',
                    'loans.approve' => 'allow',
                    'loans.disburse' => 'allow',
                    'loans.payments' => 'allow',
                    'cash.view' => 'allow',
                    'cash.operate' => 'allow',
                    'cash.close_vault' => 'allow',
                    'cash.audit' => 'allow',
                    'products.view' => 'allow',
                    'bills.view' => 'allow',
                    'bills.pay' => 'allow',
                    'statements.generate' => 'allow',
                    'tickets.view' => 'allow',
                    'tickets.respond' => 'allow',
                    'queues.manage' => 'allow',
                    'appointments.manage' => 'allow',
                    'reports.view' => 'allow',
                    'reports.generate' => 'allow',
                    'reports.download' => 'allow',
                    'security.view' => 'allow',
                ],
            ],

            'Teller' => [
                'description' => 'Bank Teller responsible for cash in/out operations, deposits, withdrawals, utility bills, and loan installment receipts.',
                'abilities' => [
                    'customers.view' => 'allow',
                    'accounts.view' => 'allow',
                    'transactions.view' => 'allow',
                    'transactions.create' => 'allow',
                    'transfers.view' => 'allow',
                    'transfers.create' => 'allow',
                    'cash.view' => 'allow',
                    'cash.operate' => 'allow',
                    'bills.view' => 'allow',
                    'bills.pay' => 'allow',
                    'loans.payments' => 'allow',
                    'statements.generate' => 'allow',
                    'queues.manage' => 'allow',
                ],
            ],

            'Loan Officer' => [
                'description' => 'Loan and Credit Officer who processes applications, evaluates creditworthiness, and tracks installments.',
                'abilities' => [
                    'customers.view' => 'allow',
                    'customers.create' => 'allow',
                    'accounts.view' => 'allow',
                    'loans.view' => 'allow',
                    'loans.create' => 'allow',
                    'loans.payments' => 'allow',
                    'statements.generate' => 'allow',
                    'reports.view' => 'allow',
                ],
            ],

            'Customer Service' => [
                'description' => 'Front Desk & Customer Relationship Agent handling onboarding, accounts opening, cards issuing, tickets, and queues.',
                'abilities' => [
                    'customers.view' => 'allow',
                    'customers.create' => 'allow',
                    'customers.update' => 'allow',
                    'accounts.view' => 'allow',
                    'accounts.create' => 'allow',
                    'cards.view' => 'allow',
                    'cards.create' => 'allow',
                    'cards.activate' => 'allow',
                    'cards.pin_manage' => 'allow',
                    'bills.view' => 'allow',
                    'bills.pay' => 'allow',
                    'statements.generate' => 'allow',
                    'tickets.view' => 'allow',
                    'tickets.respond' => 'allow',
                    'queues.manage' => 'allow',
                    'appointments.manage' => 'allow',
                ],
            ],

            'Auditor' => [
                'description' => 'Internal & Compliance Auditor with read-only visibility into banking transactions, accounts, cash audits, logs, and financial reports.',
                'abilities' => [
                    'users.view' => 'allow',
                    'branches.view' => 'allow',
                    'customers.view' => 'allow',
                    'accounts.view' => 'allow',
                    'transactions.view' => 'allow',
                    'transfers.view' => 'allow',
                    'cards.view' => 'allow',
                    'loans.view' => 'allow',
                    'cash.view' => 'allow',
                    'cash.audit' => 'allow',
                    'products.view' => 'allow',
                    'bills.view' => 'allow',
                    'reports.view' => 'allow',
                    'reports.generate' => 'allow',
                    'reports.download' => 'allow',
                    'security.view' => 'allow',
                ],
            ],
        ];

        foreach ($rolesData as $roleName => $data) {
            $role = Role::query()->firstOrCreate(['name' => $roleName]);

            // Sync abilities
            $role->updateWithAbilities([
                'name' => $roleName,
                'abilities' => $data['abilities'],
            ]);
        }
    }
}
