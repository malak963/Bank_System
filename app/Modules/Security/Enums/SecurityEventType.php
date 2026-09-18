<?php

namespace App\Modules\Security\Enums;

enum SecurityEventType: string
{
    case Login = 'login';
    case Logout = 'logout';
    case FailedLogin = 'failed_login';
    case PasswordChange = 'password_change';
    case PasswordReset = 'password_reset';
    case Transaction = 'transaction';
    case LargeTransaction = 'large_transaction';
    case UnusualLocation = 'unusual_location';
    case CardBlock = 'card_block';
    case AccountFreeze = 'account_freeze';
    case FraudAlert = 'fraud_alert';
    case SuspiciousActivity = 'suspicious_activity';
    case DataAccess = 'data_access';
    case PermissionChange = 'permission_change';
    case ApiAccess = 'api_access';
    case ConfigurationChange = 'configuration_change';

    public function label(): string
    {
        return match ($this) {
            self::Login => 'Login',
            self::Logout => 'Logout',
            self::FailedLogin => 'Failed Login',
            self::PasswordChange => 'Password Change',
            self::PasswordReset => 'Password Reset',
            self::Transaction => 'Transaction',
            self::LargeTransaction => 'Large Transaction',
            self::UnusualLocation => 'Unusual Location',
            self::CardBlock => 'Card Block',
            self::AccountFreeze => 'Account Freeze',
            self::FraudAlert => 'Fraud Alert',
            self::SuspiciousActivity => 'Suspicious Activity',
            self::DataAccess => 'Data Access',
            self::PermissionChange => 'Permission Change',
            self::ApiAccess => 'API Access',
            self::ConfigurationChange => 'Configuration Change',
        };
    }

    public function isCritical(): bool
    {
        return in_array($this, [
            self::FraudAlert,
            self::SuspiciousActivity,
            self::AccountFreeze,
            self::CardBlock,
        ]);
    }

    public function requiresImmediateAction(): bool
    {
        return in_array($this, [
            self::FraudAlert,
            self::SuspiciousActivity,
        ]);
    }

    public function isAuthentication(): bool
    {
        return in_array($this, [
            self::Login,
            self::Logout,
            self::FailedLogin,
            self::PasswordChange,
            self::PasswordReset,
        ]);
    }
}
