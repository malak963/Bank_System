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
            self::Login => __('Login'),
            self::Logout => __('Logout'),
            self::FailedLogin => __('Failed Login'),
            self::PasswordChange => __('Password Change'),
            self::PasswordReset => __('Password Reset'),
            self::Transaction => __('Transaction'),
            self::LargeTransaction => __('Large Transaction'),
            self::UnusualLocation => __('Unusual Location'),
            self::CardBlock => __('Card Block'),
            self::AccountFreeze => __('Account Freeze'),
            self::FraudAlert => __('Fraud Alert'),
            self::SuspiciousActivity => __('Suspicious Activity'),
            self::DataAccess => __('Data Access'),
            self::PermissionChange => __('Permission Change'),
            self::ApiAccess => __('API Access'),
            self::ConfigurationChange => __('Configuration Change'),
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
