<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_supported_locales_are_configured(): void
    {
        $locales = LaravelLocalization::getSupportedLocales();

        $this->assertArrayHasKey('en', $locales);
        $this->assertArrayHasKey('ar', $locales);
        $this->assertSame('Arab', $locales['ar']['script']);
        $this->assertSame('Latn', $locales['en']['script']);
    }

    public function test_login_page_renders_in_default_english(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('dir="ltr"', false);
        $response->assertSee('lang="en"', false);
        $response->assertSee(__('Sign in to continue managing your banking operations.'));
    }

    public function test_login_page_renders_in_arabic_with_rtl(): void
    {
        $this->refreshApplicationWithLocale('ar');

        $response = $this->get('/ar/login');

        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ar"', false);
        $response->assertSee('تسجيل الدخول');
        $response->assertSee('البريد الإلكتروني');
    }

    public function test_authenticated_user_can_access_dashboard_in_arabic(): void
    {
        $this->refreshApplicationWithLocale('ar');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/ar/dashboard');

        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ar"', false);
        $response->assertSee('لوحة التحكم');
    }

    public function test_authenticated_user_can_access_module_in_arabic(): void
    {
        $this->refreshApplicationWithLocale('ar');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/ar/customers');

        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ar"', false);
    }

    public function test_authenticated_user_can_access_new_modules_in_arabic(): void
    {
        $this->refreshApplicationWithLocale('ar');

        $user = User::factory()->create();

        // Branches
        $response = $this->actingAs($user)->get('/ar/branches');
        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ar"', false);

        // Transactions
        $response = $this->actingAs($user)->get('/ar/transactions');
        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('lang="ar"', false);

        // Cards
        $response = $this->actingAs($user)->get('/ar/cards');
        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('البطاقات');

        // Calculators
        $response = $this->actingAs($user)->get('/ar/calculators');
        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('حاسبة');

        // Reports
        $response = $this->actingAs($user)->get('/ar/reports');
        $response->assertOk();
        $response->assertSee('dir="rtl"', false);
        $response->assertSee('التقارير');
    }

    public function test_localized_url_generation(): void
    {
        $arabicDashboard = LaravelLocalization::getLocalizedURL('ar', route('dashboard'));
        $this->assertStringContainsString('/ar/dashboard', $arabicDashboard);

        $englishDashboard = LaravelLocalization::getLocalizedURL('en', route('dashboard'));
        $this->assertStringContainsString('/dashboard', $englishDashboard);
    }
}


